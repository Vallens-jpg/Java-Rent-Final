package drivora;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.io.File;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class MasterDataPanel extends JPanel {

    private CardLayout mainCardLayout;
    private JPanel mainCardPanel;

    // Tambah Unit Components
    private CardLayout tambahCardLayout;
    private JPanel tambahCardPanel;
    private JTextField brandField, plateField, transField, sizeField, priceField;
    private String selectedImagePath = "";
    private JLabel imageLabel;

    // Hapus Unit Components
    private CardLayout hapusCardLayout;
    private JPanel hapusCardPanel;
    private JTextField searchField;
    private int foundCarId = -1;
    private JLabel resultBrandLabel, resultTransLabel, resultPlateLabel, resultSizeLabel;

    public MasterDataPanel() {
        setLayout(new BorderLayout());
        setBackground(Theme.PRIMARY_COLOR);

        // --- TOP NAVIGATION (TABS) ---
        JPanel tabContainer = new JPanel(new FlowLayout(FlowLayout.LEFT, 0, 0));
        tabContainer.setBackground(Theme.PRIMARY_COLOR);
        tabContainer.setBorder(new EmptyBorder(10, 20, 10, 20));

        JButton tambahTabBtn = new JButton("Tambah Unit");
        JButton hapusTabBtn = new JButton("Hapus Unit");

        tambahTabBtn.setBackground(Color.WHITE);
        hapusTabBtn.setBackground(Color.LIGHT_GRAY);

        tabContainer.add(tambahTabBtn);
        tabContainer.add(hapusTabBtn);
        add(tabContainer, BorderLayout.NORTH);

        // --- MAIN CARD PANEL ---
        mainCardLayout = new CardLayout();
        mainCardPanel = new JPanel(mainCardLayout);
        mainCardPanel.setOpaque(false);

        // 1. TAMBAH UNIT PANEL
        JPanel tambahRootPanel = createTambahPanel();
        mainCardPanel.add(tambahRootPanel, "TAMBAH");

        // 2. HAPUS UNIT PANEL
        JPanel hapusRootPanel = createHapusPanel();
        mainCardPanel.add(hapusRootPanel, "HAPUS");

        add(mainCardPanel, BorderLayout.CENTER);

        // Tab Actions
        tambahTabBtn.addActionListener(e -> {
            tambahTabBtn.setBackground(Color.WHITE);
            hapusTabBtn.setBackground(Color.LIGHT_GRAY);
            mainCardLayout.show(mainCardPanel, "TAMBAH");
        });

        hapusTabBtn.addActionListener(e -> {
            hapusTabBtn.setBackground(Color.WHITE);
            tambahTabBtn.setBackground(Color.LIGHT_GRAY);
            mainCardLayout.show(mainCardPanel, "HAPUS");
        });
    }

    // ==========================================
    // TAMBAH UNIT LOGIC
    // ==========================================
    private JPanel createTambahPanel() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);

        tambahCardLayout = new CardLayout();
        tambahCardPanel = new JPanel(tambahCardLayout);
        tambahCardPanel.setOpaque(false);

        // --- Card 1: Form ---
        JPanel formCard = new JPanel(new GridBagLayout());
        formCard.setOpaque(false);
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.insets = new Insets(10, 10, 10, 10);
        gbc.weightx = 1.0;

        // Merk Mobil & Add Photo
        gbc.gridx = 0; gbc.gridy = 0; gbc.gridwidth = 1;
        formCard.add(new JLabel("Merk Mobil"), gbc);
        
        JButton addPhotoBtn = new JButton("+ Add Photo");
        addPhotoBtn.setContentAreaFilled(false);
        addPhotoBtn.setBorderPainted(false);
        addPhotoBtn.addActionListener(e -> tambahCardLayout.show(tambahCardPanel, "IMAGE"));
        gbc.gridx = 1; 
        formCard.add(addPhotoBtn, gbc);

        gbc.gridx = 0; gbc.gridy = 1; gbc.gridwidth = 2;
        brandField = new JTextField();
        formCard.add(brandField, gbc);

        // Plat Nomor
        gbc.gridy = 2; formCard.add(new JLabel("Plat Nomor"), gbc);
        gbc.gridy = 3; plateField = new JTextField(); formCard.add(plateField, gbc);

        // Transmisi
        gbc.gridy = 4; formCard.add(new JLabel("Jenis Transmisi"), gbc);
        gbc.gridy = 5; transField = new JTextField(); formCard.add(transField, gbc);

        // Jumlah Kursi
        gbc.gridy = 6; formCard.add(new JLabel("Jumlah Kursi"), gbc);
        gbc.gridy = 7; sizeField = new JTextField(); formCard.add(sizeField, gbc);

        // Periode & Harga (Split)
        JPanel splitPanel = new JPanel(new GridLayout(1, 2, 10, 0));
        splitPanel.setOpaque(false);
        
        JPanel p1 = new JPanel(new BorderLayout()); p1.setOpaque(false);
        p1.add(new JLabel("Periode"), BorderLayout.NORTH);
        JTextField periodeField = new JTextField("Jam");
        periodeField.setEditable(false);
        p1.add(periodeField, BorderLayout.CENTER);
        
        JPanel p2 = new JPanel(new BorderLayout()); p2.setOpaque(false);
        p2.add(new JLabel("Harga"), BorderLayout.NORTH);
        priceField = new JTextField();
        p2.add(priceField, BorderLayout.CENTER);

        splitPanel.add(p1); splitPanel.add(p2);
        gbc.gridy = 8; formCard.add(splitPanel, gbc);

        // Konfirmasi Btn
        JButton confirmFormBtn = new JButton("Konfirmasi");
        confirmFormBtn.setBackground(Theme.BUTTON_SUCCESS);
        confirmFormBtn.setForeground(Theme.TEXT_DARK);
        confirmFormBtn.addActionListener(e -> insertNewUnit());
        gbc.gridy = 9; gbc.insets = new Insets(30, 10, 10, 10);
        formCard.add(confirmFormBtn, gbc);

        // --- Card 2: Image Upload ---
        JPanel imageCard = new JPanel(new BorderLayout(20, 20));
        imageCard.setBorder(new EmptyBorder(40, 40, 40, 40));
        imageCard.setOpaque(false);

        JButton uploadAreaBtn = new JButton("<html><center><font size='24'>+</font><br>Tambahkan Gambar</center></html>");
        uploadAreaBtn.setBackground(Color.WHITE);
        uploadAreaBtn.setFocusPainted(false);
        uploadAreaBtn.addActionListener(e -> {
            JFileChooser chooser = new JFileChooser();
            if (chooser.showOpenDialog(this) == JFileChooser.APPROVE_OPTION) {
                File file = chooser.getSelectedFile();
                selectedImagePath = file.getAbsolutePath();
                uploadAreaBtn.setText(file.getName());
            }
        });
        
        JButton confirmImageBtn = new JButton("Konfirmasi");
        confirmImageBtn.setBackground(Theme.BUTTON_SUCCESS);
        confirmImageBtn.setForeground(Theme.TEXT_DARK);
        confirmImageBtn.addActionListener(e -> insertNewUnit());

        JButton backToFormBtn = new JButton("Kembali ke Form");
        backToFormBtn.addActionListener(e -> tambahCardLayout.show(tambahCardPanel, "FORM"));

        JPanel imageBottomPanel = new JPanel(new GridLayout(1, 2, 10, 0));
        imageBottomPanel.setOpaque(false);
        imageBottomPanel.add(backToFormBtn);
        imageBottomPanel.add(confirmImageBtn);

        imageCard.add(uploadAreaBtn, BorderLayout.CENTER);
        imageCard.add(imageBottomPanel, BorderLayout.SOUTH);

        // --- Card 3: Success ---
        JPanel successCard = new JPanel(new GridBagLayout());
        successCard.setOpaque(false);
        GridBagConstraints gbcS = new GridBagConstraints();
        gbcS.insets = new Insets(10, 10, 10, 10);
        gbcS.fill = GridBagConstraints.HORIZONTAL;

        JLabel successLabel = new JLabel("Unit Berhasilkan Ditambahkan", SwingConstants.CENTER);
        successLabel.setFont(Theme.FONT_HEADER);
        successLabel.setOpaque(true);
        successLabel.setBackground(new Color(180, 220, 180));
        successLabel.setBorder(BorderFactory.createEmptyBorder(40, 60, 40, 60));

        JButton backBtn = new JButton("Kembali");
        backBtn.setBackground(Theme.BUTTON_SUCCESS);
        backBtn.setForeground(Theme.TEXT_LIGHT);
        backBtn.addActionListener(e -> {
            // Reset form
            brandField.setText(""); plateField.setText(""); transField.setText(""); sizeField.setText(""); priceField.setText("");
            selectedImagePath = "";
            tambahCardLayout.show(tambahCardPanel, "FORM");
        });

        gbcS.gridy = 0; successCard.add(successLabel, gbcS);
        gbcS.gridy = 1; successCard.add(backBtn, gbcS);

        // Add to Card Layout
        tambahCardPanel.add(formCard, "FORM");
        tambahCardPanel.add(imageCard, "IMAGE");
        tambahCardPanel.add(successCard, "SUCCESS");

        panel.add(tambahCardPanel, BorderLayout.CENTER);
        return panel;
    }

    private void insertNewUnit() {
        String brand = brandField.getText();
        String plate = plateField.getText();
        String trans = transField.getText();
        String size = sizeField.getText();
        String priceStr = priceField.getText();

        if (brand.isEmpty() || plate.isEmpty() || priceStr.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Mohon lengkapi data minimal Merk, Plat, dan Harga!", "Peringatan", JOptionPane.WARNING_MESSAGE);
            return;
        }

        // Clean Price String (Remove dots, commas, non-digits)
        priceStr = priceStr.replaceAll("[^\\d]", "");
        if (priceStr.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Harga tidak valid!", "Error", JOptionPane.ERROR_MESSAGE);
            return;
        }
        double finalPrice = Double.parseDouble(priceStr);

        // Handle Image Copy to Laravel Storage
        String finalImagePath = "";
        if (!selectedImagePath.isEmpty()) {
            try {
                File source = new File(selectedImagePath);
                String fileName = System.currentTimeMillis() + "_" + source.getName();
                String destDirPath = "d:/coding/Documentation/coolyeah/UAS/rental-mobil/storage/app/public/cars/";
                File destDir = new File(destDirPath);
                if (!destDir.exists()) destDir.mkdirs();
                
                java.nio.file.Files.copy(source.toPath(), new File(destDir, fileName).toPath(), java.nio.file.StandardCopyOption.REPLACE_EXISTING);
                finalImagePath = "cars/" + fileName;
            } catch (Exception e) {
                e.printStackTrace();
                // Fallback to absolute path if copy fails
                finalImagePath = selectedImagePath;
            }
        }

        try (Connection conn = Database.getConnection()) {
            String sql = "INSERT INTO cars (brand, size, transmission, plate_number, price_per_hour, status, image, created_at, updated_at) " +
                         "VALUES (?, ?, ?, ?, ?, 'available', ?, NOW(), NOW())";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setString(1, brand);
                stmt.setString(2, size);
                stmt.setString(3, trans);
                stmt.setString(4, plate);
                stmt.setDouble(5, finalPrice);
                stmt.setString(6, finalImagePath);
                stmt.executeUpdate();
                
                tambahCardLayout.show(tambahCardPanel, "SUCCESS");
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal menambahkan unit:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

    // ==========================================
    // HAPUS UNIT LOGIC
    // ==========================================
    private JPanel createHapusPanel() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);

        hapusCardLayout = new CardLayout();
        hapusCardPanel = new JPanel(hapusCardLayout);
        hapusCardPanel.setOpaque(false);

        // --- Card 1: Search ---
        JPanel searchCard = new JPanel(new GridBagLayout());
        searchCard.setOpaque(false);
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 10, 10, 10);
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.weightx = 1.0;

        searchField = new JTextField();
        searchField.setPreferredSize(new Dimension(300, 35));
        searchField.setToolTipText("Masukkan Merk atau Plat Nomor");

        JButton searchBtn = new JButton("Cari Unit");
        searchBtn.setBackground(new Color(180, 220, 180));
        searchBtn.addActionListener(e -> searchUnit());

        gbc.gridy = 0; searchCard.add(searchField, gbc);
        gbc.gridy = 1; searchCard.add(searchBtn, gbc);

        // --- Card 2: Result ---
        JPanel resultCard = new JPanel(new GridBagLayout());
        resultCard.setOpaque(false);
        
        JPanel cardDisplay = new JPanel(new BorderLayout(20, 20));
        cardDisplay.setBackground(Theme.CARD_COLOR);
        cardDisplay.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(Theme.TEXT_DARK, 1),
            new EmptyBorder(20, 20, 20, 20)
        ));

        JLabel iconLabel = new JLabel("🚗", SwingConstants.CENTER);
        iconLabel.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 64));

        JPanel infoPanel = new JPanel(new GridLayout(4, 1));
        infoPanel.setOpaque(false);
        resultBrandLabel = new JLabel("-");
        resultTransLabel = new JLabel("-");
        resultPlateLabel = new JLabel("-");
        resultSizeLabel = new JLabel("-");
        infoPanel.add(resultBrandLabel);
        infoPanel.add(resultTransLabel);
        infoPanel.add(resultPlateLabel);
        infoPanel.add(resultSizeLabel);

        cardDisplay.add(iconLabel, BorderLayout.WEST);
        cardDisplay.add(infoPanel, BorderLayout.CENTER);

        JButton deleteBtn = new JButton("DELETE UNIT");
        deleteBtn.setBackground(new Color(220, 50, 50));
        deleteBtn.setForeground(Color.WHITE);
        deleteBtn.setFont(Theme.FONT_SUBHEADER);
        deleteBtn.addActionListener(e -> deleteUnit());

        gbc.gridy = 0; resultCard.add(cardDisplay, gbc);
        gbc.gridy = 1; resultCard.add(deleteBtn, gbc);
        
        JButton backFromResBtn = new JButton("Batal");
        backFromResBtn.addActionListener(e -> hapusCardLayout.show(hapusCardPanel, "SEARCH"));
        gbc.gridy = 2; resultCard.add(backFromResBtn, gbc);

        // --- Card 3: Success ---
        JPanel successCard = new JPanel(new GridBagLayout());
        successCard.setOpaque(false);

        JLabel successLabel = new JLabel("Unit Berhasil Dihapus!", SwingConstants.CENTER);
        successLabel.setFont(Theme.FONT_HEADER);
        successLabel.setOpaque(true);
        successLabel.setBackground(new Color(255, 180, 180)); // Reddish success like mockup
        successLabel.setBorder(BorderFactory.createEmptyBorder(40, 60, 40, 60));

        JButton backToSearchBtn = new JButton("Kembali");
        backToSearchBtn.setBackground(new Color(180, 220, 180));
        backToSearchBtn.addActionListener(e -> {
            searchField.setText("");
            hapusCardLayout.show(hapusCardPanel, "SEARCH");
        });

        gbc.gridy = 0; successCard.add(successLabel, gbc);
        gbc.gridy = 1; successCard.add(backToSearchBtn, gbc);

        // Add to Card Layout
        hapusCardPanel.add(searchCard, "SEARCH");
        hapusCardPanel.add(resultCard, "RESULT");
        hapusCardPanel.add(successCard, "SUCCESS");

        panel.add(hapusCardPanel, BorderLayout.CENTER);
        return panel;
    }

    private void searchUnit() {
        String query = searchField.getText().trim();
        if (query.isEmpty()) return;

        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT id, brand, transmission, plate_number, size FROM cars WHERE brand LIKE ? OR plate_number LIKE ? LIMIT 1";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setString(1, "%" + query + "%");
                stmt.setString(2, "%" + query + "%");
                ResultSet rs = stmt.executeQuery();
                
                if (rs.next()) {
                    foundCarId = rs.getInt("id");
                    resultBrandLabel.setText(rs.getString("brand"));
                    resultTransLabel.setText(rs.getString("transmission"));
                    resultPlateLabel.setText(rs.getString("plate_number"));
                    resultSizeLabel.setText(rs.getString("size") + " Seat");
                    
                    hapusCardLayout.show(hapusCardPanel, "RESULT");
                } else {
                    JOptionPane.showMessageDialog(this, "Mobil tidak ditemukan!", "Pencarian", JOptionPane.INFORMATION_MESSAGE);
                }
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Error pencarian:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
        }
    }

    private void deleteUnit() {
        if (foundCarId == -1) return;
        
        int confirm = JOptionPane.showConfirmDialog(this, "Yakin ingin menghapus unit ini secara permanen?", "Konfirmasi Hapus", JOptionPane.YES_NO_OPTION, JOptionPane.WARNING_MESSAGE);
        if (confirm != JOptionPane.YES_OPTION) return;

        try (Connection conn = Database.getConnection()) {
            // Because of foreign keys in rentals table, deleting might fail if the car has been rented.
            // In a real system we might soft delete. For now, we attempt a hard delete.
            String sql = "DELETE FROM cars WHERE id = ?";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setInt(1, foundCarId);
                stmt.executeUpdate();
                hapusCardLayout.show(hapusCardPanel, "SUCCESS");
            }
        } catch (Exception ex) {
            // Check if foreign key constraint failed
            if (ex.getMessage().contains("foreign key constraint fails")) {
                JOptionPane.showMessageDialog(this, "Unit tidak bisa dihapus karena masih memiliki riwayat transaksi/penyewaan!", "Error Database", JOptionPane.ERROR_MESSAGE);
            } else {
                JOptionPane.showMessageDialog(this, "Gagal menghapus unit:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
            }
            ex.printStackTrace();
        }
    }
}
