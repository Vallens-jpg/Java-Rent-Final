/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/GUIForms/JPanel.java to edit this template
 */
package projectrental;

/**
 *
 * @author Axioo
 */
public class CarCardGUI extends javax.swing.JPanel {

    public void setData(String brand, String transmission, double price, String plate, String statusMobil, String imagePath) {
        merk.setText(brand);
        detail.setText(transmission + " | " + plate);

        java.text.NumberFormat nf = java.text.NumberFormat.getCurrencyInstance(new java.util.Locale("id", "ID"));
        harga.setText(nf.format(price).replace(",00", "") + " / Hari");

        status.setText("  " + statusMobil.toUpperCase() + "  ");
        if (statusMobil.equalsIgnoreCase("Available")) {
            status.setBackground(new java.awt.Color(220, 245, 220));
            status.setForeground(new java.awt.Color(39, 174, 96));
        } else {
            status.setBackground(new java.awt.Color(253, 235, 235));
            status.setForeground(new java.awt.Color(192, 57, 43));
        }

        boolean imageLoaded = false;
        if (imagePath != null && !imagePath.trim().isEmpty()) {
            try {
                String fullPath = "../rental-mobil/storage/app/public/" + imagePath;
                java.io.File file = new java.io.File(fullPath);
                System.out.println("[DEBUG] Trying: " + file.getAbsolutePath() + " | exists: " + file.exists());

                if (file.exists()) {
                    java.awt.Image loadedImg = null;

                    // Tier 1: ImageIO.read (best quality, supports JPG/PNG standard)
                    try {
                        java.awt.image.BufferedImage bimg = javax.imageio.ImageIO.read(file);
                        if (bimg != null) loadedImg = bimg;
                    } catch (Exception e1) { /* ignore, try next */ }

                    // Tier 2: Toolkit.createImage (native loader, supports more PNG variants)
                    if (loadedImg == null) {
                        try {
                            java.awt.Image tkImg = java.awt.Toolkit.getDefaultToolkit().createImage(file.getAbsolutePath());
                            java.awt.MediaTracker tracker = new java.awt.MediaTracker(new java.awt.Label());
                            tracker.addImage(tkImg, 0);
                            tracker.waitForID(0, 3000);
                            if (tracker.statusID(0, false) == java.awt.MediaTracker.COMPLETE) {
                                loadedImg = tkImg;
                            }
                        } catch (Exception e2) { /* ignore */ }
                    }

                    // Tier 3: ImageIcon (uses native platform loader)
                    if (loadedImg == null) {
                        try {
                            javax.swing.ImageIcon icon = new javax.swing.ImageIcon(file.getAbsolutePath());
                            java.awt.MediaTracker tracker = new java.awt.MediaTracker(new java.awt.Label());
                            tracker.addImage(icon.getImage(), 0);
                            tracker.waitForID(0, 3000);
                            if (icon.getIconWidth() > 0) {
                                loadedImg = icon.getImage();
                            }
                        } catch (Exception e3) { /* ignore */ }
                    }

                    if (loadedImg != null) {
                        java.awt.Image scaledImg = loadedImg.getScaledInstance(160, 100, java.awt.Image.SCALE_SMOOTH);
                        Gambar.setIcon(new javax.swing.ImageIcon(scaledImg));
                        Gambar.setText("");
                        Gambar.setBorder(null);
                        imageLoaded = true;
                        System.out.println("[OK] Image loaded: " + imagePath);
                    } else {
                        System.out.println("[FAIL] All loaders failed for: " + fullPath);
                    }
                } else {
                    System.out.println("[WARN] File not found: " + file.getAbsolutePath());
                }
            } catch (Exception ex) {
                System.out.println("[ERROR] " + ex.getMessage());
            }
        }

        if (!imageLoaded) {
            Gambar.setIcon(null);
            Gambar.setText("No Photo");
            Gambar.setFont(new java.awt.Font("Segoe UI", java.awt.Font.BOLD, 12));
            Gambar.setForeground(new java.awt.Color(150, 150, 150));
            Gambar.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(230, 230, 230)));
        }
    }

    /**
     * Creates new form CarCardGUI
     */
    public CarCardGUI() {
        initComponents();
        customizeLayout();
    }

    /**
     * Customizes layout AFTER initComponents() so NetBeans .form file is not affected.
     * Replaces GroupLayout with BorderLayout for proper image display.
     */
    private void customizeLayout() {
        setBackground(new java.awt.Color(255, 255, 255));
        setBorder(javax.swing.BorderFactory.createCompoundBorder(
            javax.swing.BorderFactory.createLineBorder(new java.awt.Color(230, 230, 230), 1, true),
            javax.swing.BorderFactory.createEmptyBorder(10, 10, 10, 10)
        ));
        setPreferredSize(new java.awt.Dimension(180, 250));
        setLayout(new java.awt.BorderLayout(5, 5));

        Gambar.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        Gambar.setPreferredSize(new java.awt.Dimension(160, 100));
        Gambar.setText("");
        add(Gambar, java.awt.BorderLayout.NORTH);

        javax.swing.JPanel contentPanel = new javax.swing.JPanel();
        contentPanel.setOpaque(false);
        contentPanel.setLayout(new javax.swing.BoxLayout(contentPanel, javax.swing.BoxLayout.Y_AXIS));

        merk.setFont(new java.awt.Font("Segoe UI", java.awt.Font.BOLD, 14));
        merk.setForeground(new java.awt.Color(44, 62, 80));
        merk.setAlignmentX(java.awt.Component.CENTER_ALIGNMENT);
        contentPanel.add(merk);
        contentPanel.add(javax.swing.Box.createVerticalStrut(2));

        detail.setFont(new java.awt.Font("Segoe UI", java.awt.Font.PLAIN, 11));
        detail.setForeground(new java.awt.Color(127, 140, 141));
        detail.setAlignmentX(java.awt.Component.CENTER_ALIGNMENT);
        contentPanel.add(detail);
        contentPanel.add(javax.swing.Box.createVerticalStrut(5));

        status.setFont(new java.awt.Font("Segoe UI", java.awt.Font.BOLD, 10));
        status.setOpaque(true);
        status.setAlignmentX(java.awt.Component.CENTER_ALIGNMENT);
        contentPanel.add(status);
        contentPanel.add(javax.swing.Box.createVerticalStrut(5));

        harga.setFont(new java.awt.Font("Segoe UI", java.awt.Font.BOLD, 13));
        harga.setForeground(new java.awt.Color(41, 128, 185));
        harga.setAlignmentX(java.awt.Component.CENTER_ALIGNMENT);
        contentPanel.add(harga);

        add(contentPanel, java.awt.BorderLayout.CENTER);
    }

    /**
     * This method is called from within the constructor to initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is always
     * regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        Gambar = new javax.swing.JLabel();
        merk = new javax.swing.JLabel();
        detail = new javax.swing.JLabel();
        status = new javax.swing.JLabel();
        harga = new javax.swing.JLabel();

        setBackground(new java.awt.Color(255, 255, 255));
        setBorder(javax.swing.BorderFactory.createCompoundBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(204, 204, 204)), javax.swing.BorderFactory.createEmptyBorder(15, 15, 15, 15)));
        setPreferredSize(new java.awt.Dimension(180, 240));

        Gambar.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        Gambar.setText("jLabel1");

        merk.setBackground(new java.awt.Color(44, 62, 80));
        merk.setFont(new java.awt.Font("Segoe UI", 3, 16)); // NOI18N
        merk.setText("jLabel1");

        detail.setText("jLabel1");

        status.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        status.setText("jLabel1");
        status.setOpaque(true);

        harga.setText("jLabel1");

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(this);
        this.setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap(136, Short.MAX_VALUE)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                        .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addComponent(Gambar, javax.swing.GroupLayout.PREFERRED_SIZE, 102, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addGroup(layout.createSequentialGroup()
                                .addGap(30, 30, 30)
                                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                                    .addComponent(detail, javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(status, javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(harga, javax.swing.GroupLayout.Alignment.LEADING))))
                        .addGap(130, 130, 130))
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                        .addComponent(merk)
                        .addGap(156, 156, 156))))
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addComponent(Gambar, javax.swing.GroupLayout.PREFERRED_SIZE, 76, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addComponent(merk)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(detail)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(status)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(harga)
                .addGap(0, 92, Short.MAX_VALUE))
        );
    }// </editor-fold>//GEN-END:initComponents


    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JLabel Gambar;
    private javax.swing.JLabel detail;
    private javax.swing.JLabel harga;
    private javax.swing.JLabel merk;
    private javax.swing.JLabel status;
    // End of variables declaration//GEN-END:variables
}
