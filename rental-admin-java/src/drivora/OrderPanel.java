package drivora;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.io.File;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.text.NumberFormat;
import java.util.Locale;

public class OrderPanel extends JPanel {

    private JPanel listPanel;
    private JPanel detailPanel;
    private int selectedRentalId = -1;
    private int selectedUserId = -1;
    private int selectedCarId = -1;
    private double originalPrice = 0;
    private String selectedKtpPath = "";
    
    // Form fields
    private JTextField nameField;
    private JTextField phoneField;
    private JTextField addressField;
    private JTextField nikField;
    private JLabel ktpPathLabel;

    private int currentAdminId;

    public OrderPanel(int adminId) {
        this.currentAdminId = adminId;
        setLayout(new BorderLayout(20, 0));
        setBackground(Theme.PRIMARY_COLOR);
        setBorder(new EmptyBorder(20, 20, 20, 20));

        // --- LEFT PANEL (Pending Orders List) ---
        listPanel = new JPanel();
        listPanel.setLayout(new BoxLayout(listPanel, BoxLayout.Y_AXIS));
        listPanel.setBackground(Theme.PRIMARY_COLOR);
        
        JScrollPane listScroll = new JScrollPane(listPanel);
        listScroll.setPreferredSize(new Dimension(350, 0));
        listScroll.setBorder(null);
        listScroll.getViewport().setBackground(Theme.PRIMARY_COLOR);

        // --- RIGHT PANEL (Order Details Form) ---
        detailPanel = new JPanel(new GridBagLayout());
        detailPanel.setBackground(Theme.CARD_COLOR);
        detailPanel.setBorder(BorderFactory.createEmptyBorder(20, 30, 20, 30));
        
        // Hide detail panel initially
        detailPanel.setVisible(false);

        add(listScroll, BorderLayout.WEST);
        add(detailPanel, BorderLayout.CENTER);

        loadPendingOrders();
    }

    public void loadPendingOrders() {
        listPanel.removeAll();
        
        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT r.id, r.user_id, c.id as car_id, c.brand, c.transmission, r.total_price, TIMESTAMPDIFF(HOUR, r.start_time, r.end_time) as duration " +
                         "FROM rentals r JOIN cars c ON r.car_id = c.id " +
                         "WHERE r.status = 'pending'";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                ResultSet rs = stmt.executeQuery();
                boolean hasOrders = false;
                
                while (rs.next()) {
                    hasOrders = true;
                    int rentalId = rs.getInt("id");
                    int userId = rs.getInt("user_id");
                    int carId = rs.getInt("car_id");
                    String brand = rs.getString("brand");
                    String trans = rs.getString("transmission");
                    double price = rs.getDouble("total_price");
                    int duration = rs.getInt("duration");

                    JPanel item = createOrderItem(rentalId, userId, carId, brand, trans, price, duration);
                    listPanel.add(item);
                    listPanel.add(Box.createRigidArea(new Dimension(0, 10)));
                }
                
                if (!hasOrders) {
                    JLabel emptyLabel = new JLabel("Belum ada pesanan masuk", SwingConstants.CENTER);
                    emptyLabel.setFont(Theme.FONT_SUBHEADER);
                    emptyLabel.setForeground(Theme.TEXT_DARK);
                    emptyLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
                    listPanel.add(Box.createRigidArea(new Dimension(0, 50)));
                    listPanel.add(emptyLabel);
                    detailPanel.setVisible(false);
                }
            }
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        
        listPanel.revalidate();
        listPanel.repaint();
    }

    private JPanel createOrderItem(int rentalId, int userId, int carId, String brand, String trans, double price, int duration) {
        JPanel panel = new JPanel(new BorderLayout(10, 10));
        panel.setBackground(Theme.CARD_COLOR);
        panel.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(Theme.TEXT_DARK, 1),
            BorderFactory.createEmptyBorder(10, 10, 10, 10)
        ));
        panel.setMaximumSize(new Dimension(320, 80));
        panel.setCursor(new Cursor(Cursor.HAND_CURSOR));

        JLabel iconLabel = new JLabel("🚗", SwingConstants.CENTER);
        iconLabel.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 32));

        JPanel textPanel = new JPanel(new GridLayout(2, 1));
        textPanel.setOpaque(false);
        textPanel.add(new JLabel(brand));
        textPanel.add(new JLabel(trans));

        JPanel rightPanel = new JPanel(new GridLayout(2, 1));
        rightPanel.setOpaque(false);
        rightPanel.add(new JLabel(duration + " Hours", SwingConstants.RIGHT));
        
        NumberFormat nf = NumberFormat.getCurrencyInstance(new Locale("id", "ID"));
        JLabel priceLabel = new JLabel(nf.format(price), SwingConstants.RIGHT);
        priceLabel.setForeground(Theme.BUTTON_SUCCESS);
        rightPanel.add(priceLabel);

        panel.add(iconLabel, BorderLayout.WEST);
        panel.add(textPanel, BorderLayout.CENTER);
        panel.add(rightPanel, BorderLayout.EAST);

        // Click event to show details
        panel.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                showOrderDetails(rentalId, userId, carId, brand, trans, price, duration);
            }
        });

        return panel;
    }

    private void showOrderDetails(int rentalId, int userId, int carId, String brand, String trans, double price, int duration) {
        selectedRentalId = rentalId;
        selectedUserId = userId;
        selectedCarId = carId;
        originalPrice = price;
        
        detailPanel.removeAll();
        detailPanel.setVisible(true);

        GridBagConstraints gbc = new GridBagConstraints();
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.insets = new Insets(10, 5, 10, 5);
        gbc.weightx = 1.0;

        // Top Header
        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setOpaque(false);
        headerPanel.add(new JLabel("🚗 " + brand + " (" + trans + ")"), BorderLayout.WEST);
        
        NumberFormat nf = NumberFormat.getCurrencyInstance(new Locale("id", "ID"));
        headerPanel.add(new JLabel(duration + " Hours - " + nf.format(price)), BorderLayout.EAST);
        
        gbc.gridx = 0; gbc.gridy = 0;
        detailPanel.add(headerPanel, gbc);

        // Fetch User Data
        String userName = "", userPhone = "", userAddress = "", userNik = "";
        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT name, phone, address, nik FROM users WHERE id = ?";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setInt(1, userId);
                ResultSet rs = stmt.executeQuery();
                if (rs.next()) {
                    userName = rs.getString("name");
                    userPhone = rs.getString("phone");
                    userAddress = rs.getString("address");
                    userNik = rs.getString("nik");
                }
            }
        } catch (Exception ex) {
            ex.printStackTrace();
        }

        // Form Fields
        gbc.gridy = 1; detailPanel.add(new JLabel("Nama Sesuai KTP"), gbc);
        nameField = new JTextField(userName != null ? userName : "");
        gbc.gridy = 2; detailPanel.add(nameField, gbc);

        gbc.gridy = 3; detailPanel.add(new JLabel("Nomor telepon"), gbc);
        phoneField = new JTextField(userPhone != null ? userPhone : "");
        gbc.gridy = 4; detailPanel.add(phoneField, gbc);

        gbc.gridy = 5; detailPanel.add(new JLabel("Alamat Lengkap"), gbc);
        addressField = new JTextField(userAddress != null ? userAddress : "");
        gbc.gridy = 6; detailPanel.add(addressField, gbc);

        gbc.gridy = 7; detailPanel.add(new JLabel("Nomor KTP"), gbc);
        nikField = new JTextField(userNik != null ? userNik : "");
        gbc.gridy = 8; detailPanel.add(nikField, gbc);

        // Upload KTP Panel
        JPanel ktpPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        ktpPanel.setOpaque(false);
        JButton uploadBtn = new JButton("+ Tambahkan Foto KTP");
        ktpPathLabel = new JLabel("Belum ada file dipilih");
        ktpPathLabel.setFont(Theme.FONT_SMALL);
        
        uploadBtn.addActionListener(e -> {
            JFileChooser fileChooser = new JFileChooser();
            if (fileChooser.showOpenDialog(this) == JFileChooser.APPROVE_OPTION) {
                File file = fileChooser.getSelectedFile();
                selectedKtpPath = file.getAbsolutePath();
                ktpPathLabel.setText(file.getName());
            }
        });
        
        ktpPanel.add(uploadBtn);
        ktpPanel.add(ktpPathLabel);
        
        gbc.gridy = 9; detailPanel.add(ktpPanel, gbc);

        // Confirm and Reject Buttons
        JPanel btnPanel = new JPanel(new GridLayout(1, 2, 10, 0));
        btnPanel.setOpaque(false);
        
        JButton confirmBtn = new JButton("Konfirmasi");
        confirmBtn.setBackground(Theme.BUTTON_SUCCESS);
        confirmBtn.setForeground(Theme.TEXT_DARK);
        confirmBtn.setFont(Theme.FONT_SUBHEADER);
        confirmBtn.addActionListener(e -> confirmOrder());

        JButton rejectBtn = new JButton("Tolak Pesanan");
        rejectBtn.setBackground(new Color(239, 68, 68)); // Tailwind red-500
        rejectBtn.setForeground(Color.DARK_GRAY);
        rejectBtn.setFont(Theme.FONT_SUBHEADER);
        rejectBtn.addActionListener(e -> rejectOrder());
        
        btnPanel.add(rejectBtn);
        btnPanel.add(confirmBtn);
        
        gbc.gridy = 10;
        gbc.insets = new Insets(30, 5, 10, 5);
        detailPanel.add(btnPanel, gbc);

        detailPanel.revalidate();
        detailPanel.repaint();
    }

    private void confirmOrder() {
        if (selectedRentalId == -1 || selectedUserId == -1) return;
        
        String address = addressField.getText();
        String nik = nikField.getText();

        if (address.isEmpty() || nik.isEmpty() || selectedKtpPath.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Mohon lengkapi Alamat, NIK, dan unggah Foto KTP!", "Peringatan", JOptionPane.WARNING_MESSAGE);
            return;
        }

        try (Connection conn = Database.getConnection()) {
            conn.setAutoCommit(false); // Transaction
            try {
                // Update User
                String userSql = "UPDATE users SET address = ?, nik = ?, ktp_photo = ? WHERE id = ?";
                try (PreparedStatement stmt = conn.prepareStatement(userSql)) {
                    stmt.setString(1, address);
                    stmt.setString(2, nik);
                    stmt.setString(3, selectedKtpPath); // Simpan path lokal (Atau copy file ke direktori khusus di production)
                    stmt.setInt(4, selectedUserId);
                    stmt.executeUpdate();
                }

                // Update Rental Status
                String rentalSql = "UPDATE rentals SET status = 'active', admin_id = ? WHERE id = ?";
                try (PreparedStatement stmt = conn.prepareStatement(rentalSql)) {
                    stmt.setInt(1, currentAdminId);
                    stmt.setInt(2, selectedRentalId);
                    stmt.executeUpdate();
                }

                // Insert into transactions
                String transSql = "INSERT INTO transactions (rental_id, car_id, transaction_type, amount, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
                try (PreparedStatement stmt = conn.prepareStatement(transSql)) {
                    stmt.setInt(1, selectedRentalId);
                    stmt.setInt(2, selectedCarId);
                    stmt.setString(3, "Sewa Baru");
                    stmt.setDouble(4, originalPrice);
                    stmt.executeUpdate();
                }

                // Update Car Status to rented
                String carSql = "UPDATE cars SET status = 'rented' WHERE id = (SELECT car_id FROM rentals WHERE id = ?)";
                try (PreparedStatement stmt = conn.prepareStatement(carSql)) {
                    stmt.setInt(1, selectedRentalId);
                    stmt.executeUpdate();
                }

                conn.commit();
                
                JOptionPane.showMessageDialog(this, "Pesanan Berhasil Dikonfirmasi!", "Sukses", JOptionPane.INFORMATION_MESSAGE);
                selectedRentalId = -1;
                selectedUserId = -1;
                loadPendingOrders();
                
            } catch (Exception e) {
                conn.rollback();
                throw e;
            } finally {
                conn.setAutoCommit(true);
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal mengonfirmasi pesanan:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

    private void rejectOrder() {
        if (selectedRentalId == -1) return;

        String reason = JOptionPane.showInputDialog(this, "Masukkan alasan penolakan:", "Tolak Pesanan", JOptionPane.QUESTION_MESSAGE);
        if (reason == null || reason.trim().isEmpty()) {
            JOptionPane.showMessageDialog(this, "Alasan penolakan tidak boleh kosong!", "Peringatan", JOptionPane.WARNING_MESSAGE);
            return;
        }

        try (Connection conn = Database.getConnection()) {
            String sql = "UPDATE rentals SET status = 'rejected', rejection_reason = ?, admin_id = ? WHERE id = ?";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setString(1, reason);
                stmt.setInt(2, currentAdminId);
                stmt.setInt(3, selectedRentalId);
                stmt.executeUpdate();
            }
            
            JOptionPane.showMessageDialog(this, "Pesanan berhasil ditolak.", "Informasi", JOptionPane.INFORMATION_MESSAGE);
            selectedRentalId = -1;
            selectedUserId = -1;
            detailPanel.removeAll();
            detailPanel.revalidate();
            detailPanel.repaint();
            loadPendingOrders();
            
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal menolak pesanan:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

}
