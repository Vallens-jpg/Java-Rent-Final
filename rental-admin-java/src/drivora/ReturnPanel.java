package drivora;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.text.NumberFormat;
import java.util.Locale;

public class ReturnPanel extends JPanel {

    private JPanel listPanel;
    private JPanel detailPanel;
    
    // Form fields
    private JTextField nameField;
    private JTextField phoneField;
    private JTextField addressField;
    private JTextField nikField;
    private int selectedRentalId = -1;
    private int selectedCarId = -1;
    private boolean isOverdue = false;
    private double originalPrice = 0;
    private double penaltyAmount = 100000; // Flat Rp 100.000 denda keterlambatan

    private int currentAdminId;

    public ReturnPanel(int adminId) {
        this.currentAdminId = adminId;
        setLayout(new BorderLayout(20, 0));
        setBackground(Theme.PRIMARY_COLOR);
        setBorder(new EmptyBorder(20, 20, 20, 20));

        // --- LEFT PANEL (Active Orders List) ---
        listPanel = new JPanel();
        listPanel.setLayout(new BoxLayout(listPanel, BoxLayout.Y_AXIS));
        listPanel.setBackground(Theme.PRIMARY_COLOR);
        
        JScrollPane listScroll = new JScrollPane(listPanel);
        listScroll.setPreferredSize(new Dimension(350, 0));
        listScroll.setBorder(null);
        listScroll.getViewport().setBackground(Theme.PRIMARY_COLOR);

        // --- RIGHT PANEL (Return Details Form) ---
        detailPanel = new JPanel(new GridBagLayout());
        detailPanel.setBackground(Theme.CARD_COLOR);
        detailPanel.setBorder(BorderFactory.createEmptyBorder(20, 30, 20, 30));
        
        // Hide detail panel initially
        detailPanel.setVisible(false);

        add(listScroll, BorderLayout.WEST);
        add(detailPanel, BorderLayout.CENTER);

        loadActiveOrders();
    }

    public void loadActiveOrders() {
        listPanel.removeAll();
        detailPanel.setVisible(false);
        
        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT r.id, r.user_id, c.id as car_id, c.brand, c.transmission, r.total_price, " +
                         "TIMESTAMPDIFF(SECOND, NOW(), r.end_time) as time_diff_sec, r.penalty_status, r.extension_status, r.extension_days " +
                         "FROM rentals r JOIN cars c ON r.car_id = c.id " +
                         "WHERE r.status = 'active'";
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
                    long timeDiffSec = rs.getLong("time_diff_sec");
                    String penaltyStatus = rs.getString("penalty_status");
                    String extensionStatus = rs.getString("extension_status");
                    int extensionDays = rs.getInt("extension_days");

                    JPanel item = createReturnItem(rentalId, userId, carId, brand, trans, price, timeDiffSec, penaltyStatus, extensionStatus, extensionDays);
                    listPanel.add(item);
                    listPanel.add(Box.createRigidArea(new Dimension(0, 10)));
                }
                
                if (!hasOrders) {
                    JLabel emptyLabel = new JLabel("Tidak ada unit yang sedang dirental.", SwingConstants.CENTER);
                    emptyLabel.setFont(Theme.FONT_SUBHEADER);
                    emptyLabel.setForeground(Theme.TEXT_DARK);
                    emptyLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
                    listPanel.add(Box.createRigidArea(new Dimension(0, 50)));
                    listPanel.add(emptyLabel);
                }
            }
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        
        listPanel.revalidate();
        listPanel.repaint();
    }

    private String formatTimeDiff(long totalSecs) {
        boolean isNegative = totalSecs < 0;
        long absSecs = Math.abs(totalSecs);
        long hours = absSecs / 3600;
        long minutes = (absSecs % 3600) / 60;
        long seconds = absSecs % 60;
        
        String timeStr = String.format("%02d:%02d:%02d", hours, minutes, seconds);
        return isNegative ? "-" + timeStr : timeStr;
    }

    private JPanel createReturnItem(int rentalId, int userId, int carId, String brand, String trans, double price, long timeDiffSec, String penaltyStatus, String extensionStatus, int extensionDays) {
        boolean overdue = timeDiffSec < 0;
        
        JPanel panel = new JPanel(new BorderLayout(10, 10));
        panel.setBackground(overdue ? new Color(255, 180, 180) : Theme.CARD_COLOR);
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
        
        JLabel timeLabel = new JLabel(formatTimeDiff(timeDiffSec), SwingConstants.CENTER);
        timeLabel.setOpaque(true);
        timeLabel.setBackground(overdue ? new Color(200, 0, 0) : new Color(0, 150, 0));
        timeLabel.setForeground(Color.WHITE);
        timeLabel.setFont(Theme.FONT_SMALL);
        
        rightPanel.add(timeLabel);
        
        NumberFormat nf = NumberFormat.getCurrencyInstance(new Locale("id", "ID"));
        JLabel priceLabel = new JLabel(nf.format(price), SwingConstants.RIGHT);
        priceLabel.setForeground(Theme.TEXT_DARK);
        rightPanel.add(priceLabel);

        panel.add(iconLabel, BorderLayout.WEST);
        panel.add(textPanel, BorderLayout.CENTER);
        panel.add(rightPanel, BorderLayout.EAST);

        panel.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                showReturnDetails(rentalId, userId, carId, brand, trans, price, timeDiffSec, penaltyStatus, extensionStatus, extensionDays);
            }
        });

        return panel;
    }

    private void showReturnDetails(int rentalId, int userId, int carId, String brand, String trans, double price, long timeDiffSec, String penaltyStatus, String extensionStatus, int extensionDays) {
        selectedRentalId = rentalId;
        selectedCarId = carId;
        isOverdue = timeDiffSec < 0;
        originalPrice = price;
        
        detailPanel.removeAll();
        detailPanel.setBackground(isOverdue ? new Color(255, 180, 180) : Theme.CARD_COLOR);
        detailPanel.setVisible(true);

        GridBagConstraints gbc = new GridBagConstraints();
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.insets = new Insets(10, 5, 10, 5);
        gbc.weightx = 1.0;

        // Top Header
        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setOpaque(false);
        headerPanel.add(new JLabel("🚗 " + brand + " (" + trans + ")"), BorderLayout.WEST);
        
        JPanel timePricePanel = new JPanel(new GridLayout(2, 1));
        timePricePanel.setOpaque(false);
        
        JLabel timeLabel = new JLabel(formatTimeDiff(timeDiffSec), SwingConstants.CENTER);
        timeLabel.setOpaque(true);
        timeLabel.setBackground(isOverdue ? new Color(200, 0, 0) : new Color(0, 150, 0));
        timeLabel.setForeground(Color.WHITE);
        
        NumberFormat nf = NumberFormat.getCurrencyInstance(new Locale("id", "ID"));
        JLabel priceLabel = new JLabel(nf.format(price), SwingConstants.RIGHT);
        
        timePricePanel.add(timeLabel);
        timePricePanel.add(priceLabel);
        
        if (isOverdue) {
            JLabel penaltyLabel = new JLabel("+ " + nf.format(penaltyAmount) + " (Denda)", SwingConstants.RIGHT);
            penaltyLabel.setForeground(new Color(200, 0, 0));
            timePricePanel.setLayout(new GridLayout(3, 1));
            timePricePanel.add(penaltyLabel);
        }
        
        headerPanel.add(timePricePanel, BorderLayout.EAST);
        
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

        // Form Fields (Read Only)
        gbc.gridy = 1; detailPanel.add(new JLabel("Nama Lengkap"), gbc);
        nameField = new JTextField(userName != null ? userName : "");
        nameField.setEditable(false);
        gbc.gridy = 2; detailPanel.add(nameField, gbc);

        gbc.gridy = 3; detailPanel.add(new JLabel("Nomor telepon"), gbc);
        phoneField = new JTextField(userPhone != null ? userPhone : "");
        phoneField.setEditable(false);
        gbc.gridy = 4; detailPanel.add(phoneField, gbc);

        gbc.gridy = 5; detailPanel.add(new JLabel("Alamat Lengkap"), gbc);
        addressField = new JTextField(userAddress != null ? userAddress : "");
        addressField.setEditable(false);
        gbc.gridy = 6; detailPanel.add(addressField, gbc);

        gbc.gridy = 7; detailPanel.add(new JLabel("Nomor KTP"), gbc);
        nikField = new JTextField(userNik != null ? userNik : "");
        nikField.setEditable(false);
        gbc.gridy = 8; detailPanel.add(nikField, gbc);

        // Confirm Button
        JButton confirmBtn = new JButton("Konfirmasi Pengembalian");
        confirmBtn.setBackground(Theme.BUTTON_SUCCESS);
        confirmBtn.setForeground(Theme.TEXT_DARK);
        confirmBtn.setFont(Theme.FONT_SUBHEADER);
        confirmBtn.addActionListener(e -> confirmReturn());
        
        gbc.gridy = 9;
        gbc.insets = new Insets(30, 5, 10, 5);

        // Extension Check First
        if ("pending_verification".equals(extensionStatus)) {
            confirmBtn.setEnabled(false);
            confirmBtn.setText("Menunggu Verifikasi Perpanjangan");
            
            JPanel extensionActionPanel = new JPanel(new GridLayout(2, 1, 0, 10));
            extensionActionPanel.setOpaque(false);
            
            JLabel extLabel = new JLabel("Pengajuan Perpanjangan: " + extensionDays + " Hari", SwingConstants.CENTER);
            extLabel.setFont(Theme.FONT_SUBHEADER);
            extLabel.setForeground(Theme.TEXT_DARK);
            extensionActionPanel.add(extLabel);
            
            JPanel extBtnPanel = new JPanel(new GridLayout(1, 2, 10, 0));
            extBtnPanel.setOpaque(false);
            
            JButton verifyExtBtn = new JButton("Konfirmasi Perpanjangan");
            verifyExtBtn.setBackground(new Color(34, 197, 94));
            verifyExtBtn.setForeground(Color.DARK_GRAY);
            verifyExtBtn.addActionListener(e -> updateExtensionStatus(rentalId, "approved", extensionDays));
            
            JButton rejectExtBtn = new JButton("Tolak Perpanjangan");
            rejectExtBtn.setBackground(Theme.BUTTON_DANGER);
            rejectExtBtn.setForeground(Color.DARK_GRAY);
            rejectExtBtn.addActionListener(e -> updateExtensionStatus(rentalId, "rejected", extensionDays));
            
            extBtnPanel.add(verifyExtBtn);
            extBtnPanel.add(rejectExtBtn);
            
            extensionActionPanel.add(extBtnPanel);
            
            gbc.gridy = 9; detailPanel.add(extensionActionPanel, gbc);
            gbc.gridy = 10; detailPanel.add(confirmBtn, gbc);
        } else if (isOverdue) {
            if ("pending_verification".equals(penaltyStatus)) {
                // Disable return car button until penalty is resolved
                confirmBtn.setEnabled(false);
                confirmBtn.setText("Denda Belum Diverifikasi");
                
                JPanel actionPanel = new JPanel(new GridLayout(1, 2, 10, 0));
                actionPanel.setOpaque(false);
                
                JButton verifyBtn = new JButton("Konfirmasi Denda");
                verifyBtn.setBackground(new Color(34, 197, 94));
                verifyBtn.setForeground(Color.DARK_GRAY);
                verifyBtn.addActionListener(e -> updatePenaltyStatus(rentalId, "paid"));
                
                JButton rejectBtn = new JButton("Tolak Denda");
                rejectBtn.setBackground(Theme.BUTTON_DANGER);
                rejectBtn.setForeground(Color.DARK_GRAY);
                rejectBtn.addActionListener(e -> updatePenaltyStatus(rentalId, "unpaid"));
                
                actionPanel.add(verifyBtn);
                actionPanel.add(rejectBtn);
                
                gbc.gridy = 9; detailPanel.add(actionPanel, gbc);
                gbc.gridy = 10; detailPanel.add(confirmBtn, gbc);
            } else if ("paid".equals(penaltyStatus)) {
                confirmBtn.setText("Konfirmasi Pengembalian (Denda Lunas)");
                gbc.gridy = 9; detailPanel.add(confirmBtn, gbc);
            } else {
                confirmBtn.setText("Konfirmasi Pengembalian (Denda Belum Lunas)");
                gbc.gridy = 9; detailPanel.add(confirmBtn, gbc);
            }
        } else {
            gbc.gridy = 9; detailPanel.add(confirmBtn, gbc);
        }

        detailPanel.revalidate();
        detailPanel.repaint();
    }

    private void updateExtensionStatus(int rentalId, String status, int days) {
        try (Connection conn = Database.getConnection()) {
            if ("approved".equals(status)) {
                // Tambahkan end_time dan total_price
                String sqlPrice = "SELECT c.price_per_hour FROM rentals r JOIN cars c ON r.car_id = c.id WHERE r.id = ?";
                double pricePerDay = 50000 * 24; // Default fallback
                try (PreparedStatement stmt1 = conn.prepareStatement(sqlPrice)) {
                    stmt1.setInt(1, rentalId);
                    ResultSet rs = stmt1.executeQuery();
                    if (rs.next()) {
                        pricePerDay = rs.getDouble("price_per_hour") * 24;
                    }
                }
                
                double cost = pricePerDay * days;
                
                String sql = "UPDATE rentals SET end_time = DATE_ADD(end_time, INTERVAL ? DAY), " +
                             "total_price = total_price + ?, extension_status = ?, extension_days = NULL, notification_dismissed = 0 WHERE id = ?";
                try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                    stmt.setInt(1, days);
                    stmt.setDouble(2, cost);
                    stmt.setString(3, status);
                    stmt.setInt(4, rentalId);
                    stmt.executeUpdate();
                }

                // Insert transaction
                String transSql = "INSERT INTO transactions (rental_id, car_id, transaction_type, amount, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
                try (PreparedStatement stmt = conn.prepareStatement(transSql)) {
                    stmt.setInt(1, rentalId);
                    stmt.setInt(2, selectedCarId);
                    stmt.setString(3, "Perpanjangan");
                    stmt.setDouble(4, cost);
                    stmt.executeUpdate();
                }
            } else {
                String sql = "UPDATE rentals SET extension_status = ?, extension_days = NULL, notification_dismissed = 0 WHERE id = ?";
                try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                    stmt.setString(1, status);
                    stmt.setInt(2, rentalId);
                    stmt.executeUpdate();
                }
            }
            JOptionPane.showMessageDialog(this, "Status perpanjangan berhasil diperbarui!");
            loadActiveOrders();
        } catch (Exception ex) {
            ex.printStackTrace();
            JOptionPane.showMessageDialog(this, "Gagal memperbarui status perpanjangan: " + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
        }
    }

    private void confirmReturn() {
        if (selectedRentalId == -1) return;

        double finalPrice = originalPrice;
        if (isOverdue) {
            finalPrice += penaltyAmount;
        }

        int confirm = JOptionPane.showConfirmDialog(this, 
            "Selesaikan pengembalian?\nTotal Tagihan: " + NumberFormat.getCurrencyInstance(new Locale("id", "ID")).format(finalPrice), 
            "Konfirmasi", JOptionPane.YES_NO_OPTION);
            
        if (confirm != JOptionPane.YES_OPTION) return;

        try (Connection conn = Database.getConnection()) {
            conn.setAutoCommit(false); // Transaction
            try {
                // Update Rental Status and Final Price
                String rentalSql = "UPDATE rentals SET status = 'completed', total_price = ?, admin_id = ? WHERE id = ?";
                try (PreparedStatement stmt = conn.prepareStatement(rentalSql)) {
                    stmt.setDouble(1, finalPrice);
                    stmt.setInt(2, currentAdminId);
                    stmt.setInt(3, selectedRentalId);
                    stmt.executeUpdate();
                }

                // Update Car Status back to available
                String carSql = "UPDATE cars SET status = 'available' WHERE id = (SELECT car_id FROM rentals WHERE id = ?)";
                try (PreparedStatement stmt = conn.prepareStatement(carSql)) {
                    stmt.setInt(1, selectedRentalId);
                    stmt.executeUpdate();
                }

                conn.commit();
                
                JOptionPane.showMessageDialog(this, "Pengembalian berhasil dikonfirmasi!", "Sukses", JOptionPane.INFORMATION_MESSAGE);
                loadActiveOrders();
                
            } catch (Exception e) {
                conn.rollback();
                throw e;
            } finally {
                conn.setAutoCommit(true);
            }
        } catch (Exception ex) {
                JOptionPane.showMessageDialog(this, "Gagal mengonfirmasi pengembalian:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

    private void updatePenaltyStatus(int rentalId, String status) {
        try (Connection conn = Database.getConnection()) {
            String sql = "UPDATE rentals SET penalty_status = ? WHERE id = ?";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setString(1, status);
                stmt.setInt(2, rentalId);
                stmt.executeUpdate();
                
                String msg = "paid".equals(status) ? "Pembayaran denda dikonfirmasi." : "Pembayaran denda ditolak.";
                
                if ("paid".equals(status)) {
                    // Insert transaction
                    String transSql = "INSERT INTO transactions (rental_id, car_id, transaction_type, amount, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
                    try (PreparedStatement stmt2 = conn.prepareStatement(transSql)) {
                        stmt2.setInt(1, rentalId);
                        stmt2.setInt(2, selectedCarId);
                        stmt2.setString(3, "Denda");
                        stmt2.setDouble(4, penaltyAmount);
                        stmt2.executeUpdate();
                    }
                }
                
                JOptionPane.showMessageDialog(this, msg, "Informasi", JOptionPane.INFORMATION_MESSAGE);
                loadActiveOrders();
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal memperbarui status denda:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }
}
