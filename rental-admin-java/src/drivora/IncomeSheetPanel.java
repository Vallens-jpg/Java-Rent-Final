package drivora;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.JTableHeader;
import java.awt.*;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.text.NumberFormat;
import java.text.SimpleDateFormat;
import java.util.Locale;

public class IncomeSheetPanel extends JPanel {

    private JTable table;
    private DefaultTableModel tableModel;

    public IncomeSheetPanel() {
        setLayout(new BorderLayout(20, 20));
        setBackground(Theme.PRIMARY_COLOR);
        setBorder(new EmptyBorder(20, 20, 20, 20));

        // Header Title
        JLabel titleLabel = new JLabel("Income Sheet (Laporan Pendapatan)");
        titleLabel.setFont(Theme.FONT_HEADER);
        titleLabel.setForeground(Theme.TEXT_DARK);
        add(titleLabel, BorderLayout.NORTH);

        // Table Model
        String[] columns = {"ID Order", "ID Mobil", "Mobil", "Jenis Transaksi", "Tanggal Transaksi", "Waktu Transaksi", "Periode", "Nominal", "Nama Admin"};
        tableModel = new DefaultTableModel(columns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false; // Table is read-only
            }
        };

        table = new JTable(tableModel);
        table.setRowHeight(30);
        table.setFont(Theme.FONT_REGULAR);
        table.setGridColor(Color.LIGHT_GRAY);
        
        JTableHeader header = table.getTableHeader();
        header.setFont(Theme.FONT_SUBHEADER);
        header.setBackground(Theme.CARD_COLOR);
        
        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setBorder(BorderFactory.createLineBorder(Theme.TEXT_DARK, 1));
        add(scrollPane, BorderLayout.CENTER);

        // Export Button
        JButton exportBtn = new JButton("Ekspor File CSV");
        exportBtn.setBackground(Theme.BUTTON_SUCCESS);
        exportBtn.setForeground(Theme.TEXT_DARK);
        exportBtn.setFont(Theme.FONT_SUBHEADER);
        exportBtn.addActionListener(e -> exportToCSV());
        
        JPanel bottomPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        bottomPanel.setOpaque(false);
        bottomPanel.add(exportBtn);
        add(bottomPanel, BorderLayout.SOUTH);

        loadData();
    }

    public void loadData() {
        tableModel.setRowCount(0); // Clear table
        
        NumberFormat nf = NumberFormat.getCurrencyInstance(new Locale("id", "ID"));
        SimpleDateFormat dateFormat = new SimpleDateFormat("dd MMM yyyy");
        SimpleDateFormat timeOnlyFormat = new SimpleDateFormat("HH:mm:ss");
        SimpleDateFormat timeFormat = new SimpleDateFormat("HH:mm");

        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT t.rental_id, t.car_id, c.brand, t.transaction_type, t.amount, t.created_at as trans_date, " +
                         "r.start_time, r.end_time, TIMESTAMPDIFF(HOUR, r.start_time, r.end_time) as duration, " +
                         "u.name as admin_name " +
                         "FROM transactions t " +
                         "JOIN rentals r ON t.rental_id = r.id " +
                         "JOIN cars c ON t.car_id = c.id " +
                         "LEFT JOIN users u ON r.admin_id = u.id " +
                         "ORDER BY t.created_at DESC";
                         
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                ResultSet rs = stmt.executeQuery();
                while (rs.next()) {
                    int idOrder = rs.getInt("rental_id");
                    int idMobil = rs.getInt("car_id");
                    String brand = rs.getString("brand");
                    String type = rs.getString("transaction_type");
                    double amount = rs.getDouble("amount");
                    java.sql.Timestamp transDate = rs.getTimestamp("trans_date");
                    java.sql.Timestamp start = rs.getTimestamp("start_time");
                    java.sql.Timestamp end = rs.getTimestamp("end_time");
                    int duration = rs.getInt("duration");
                    String admin = rs.getString("admin_name");
                    
                    if (admin == null || admin.isEmpty()) {
                        admin = "Sistem/Unknown";
                    }

                    String tglTransaksi = transDate != null ? dateFormat.format(transDate) : "-";
                    String waktuTransaksi = transDate != null ? timeOnlyFormat.format(transDate) : "-";
                    String periode = "";
                    if (start != null && end != null) {
                        periode = timeFormat.format(start) + " s.d " + timeFormat.format(end) + " (" + duration + " Jam)";
                    }

                    Object[] row = {
                        "TRX-" + idOrder,
                        "CAR-" + idMobil,
                        brand,
                        type,
                        tglTransaksi,
                        waktuTransaksi,
                        periode,
                        nf.format(amount),
                        admin
                    };
                    tableModel.addRow(row);
                }
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal memuat data laporan: " + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

    private void exportToCSV() {
        if (tableModel.getRowCount() == 0) {
            JOptionPane.showMessageDialog(this, "Tidak ada data untuk diekspor!", "Peringatan", JOptionPane.WARNING_MESSAGE);
            return;
        }

        JFileChooser fileChooser = new JFileChooser();
        fileChooser.setDialogTitle("Simpan Laporan Income Sheet");
        fileChooser.setSelectedFile(new File("Income_Sheet.csv"));
        
        if (fileChooser.showSaveDialog(this) == JFileChooser.APPROVE_OPTION) {
            File file = fileChooser.getSelectedFile();
            
            // Tambahkan ekstensi .csv jika belum ada
            if (!file.getName().toLowerCase().endsWith(".csv")) {
                file = new File(file.getParentFile(), file.getName() + ".csv");
            }

            try (FileWriter fw = new FileWriter(file)) {
                // Tulis Header
                for (int i = 0; i < tableModel.getColumnCount(); i++) {
                    fw.write(tableModel.getColumnName(i));
                    if (i < tableModel.getColumnCount() - 1) fw.write(",");
                }
                fw.write("\n");

                // Tulis Data
                for (int i = 0; i < tableModel.getRowCount(); i++) {
                    for (int j = 0; j < tableModel.getColumnCount(); j++) {
                        String data = tableModel.getValueAt(i, j).toString();
                        // Escape quotes and commas
                        if (data.contains(",") || data.contains("\"")) {
                            data = "\"" + data.replace("\"", "\"\"") + "\"";
                        }
                        fw.write(data);
                        if (j < tableModel.getColumnCount() - 1) fw.write(",");
                    }
                    fw.write("\n");
                }
                
                JOptionPane.showMessageDialog(this, "File CSV berhasil disimpan:\n" + file.getAbsolutePath(), "Sukses", JOptionPane.INFORMATION_MESSAGE);
            } catch (IOException ex) {
                JOptionPane.showMessageDialog(this, "Gagal menyimpan file CSV:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
                ex.printStackTrace();
            }
        }
    }
}
