package drivora;

import javax.swing.*;
import java.awt.*;
import java.net.URL;
import javax.imageio.ImageIO;

public class CarCard extends JPanel {
    private JLabel iconLabel;

    public CarCard(String brand, String transmission, String imageUrl, double price, String plate, String status) {
        setLayout(new BoxLayout(this, BoxLayout.Y_AXIS));
        setBackground(Theme.PRIMARY_COLOR);
        setBorder(BorderFactory.createLineBorder(Theme.TEXT_DARK, 1));
        
        // Placeholder Icon
        iconLabel = new JLabel("Loading Image...", SwingConstants.CENTER);
        iconLabel.setFont(Theme.FONT_SMALL);
        iconLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        iconLabel.setPreferredSize(new Dimension(120, 80));
        iconLabel.setMaximumSize(new Dimension(120, 80));

        // Load image in background to prevent freezing UI
        if (imageUrl != null && !imageUrl.isEmpty()) {
            SwingWorker<ImageIcon, Void> worker = new SwingWorker<ImageIcon, Void>() {
                @Override
                protected ImageIcon doInBackground() throws Exception {
                    Image img = null;
                    if (imageUrl.startsWith("http")) {
                        URL url = new URL(imageUrl);
                        img = ImageIO.read(url);
                    } else {
                        // It's a local file relative to Laravel's storage
                        String localPath = "d:/coding/Documentation/coolyeah/UAS/rental-mobil/storage/app/public/" + imageUrl;
                        java.io.File file = new java.io.File(localPath);
                        if (file.exists()) {
                            img = ImageIO.read(file);
                        }
                    }
                    
                    if (img != null) {
                        Image scaledImg = img.getScaledInstance(120, 80, Image.SCALE_SMOOTH);
                        return new ImageIcon(scaledImg);
                    }
                    return null;
                }

                @Override
                protected void done() {
                    try {
                        ImageIcon icon = get();
                        if (icon != null) {
                            iconLabel.setText("");
                            iconLabel.setIcon(icon);
                        } else {
                            iconLabel.setText("No Image");
                        }
                    } catch (Exception e) {
                        iconLabel.setText("Image Error");
                    }
                }
            };
            worker.execute();
        } else {
            iconLabel.setText("No Image");
        }

        JLabel brandLabel = new JLabel(brand, SwingConstants.CENTER);
        brandLabel.setFont(Theme.FONT_REGULAR);
        brandLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        brandLabel.setForeground(Theme.TEXT_DARK);

        JLabel transLabel = new JLabel(transmission.equalsIgnoreCase("Automatic") ? "Matic" : "Manual", SwingConstants.CENTER);
        transLabel.setFont(Theme.FONT_REGULAR);
        transLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        transLabel.setForeground(Theme.TEXT_DARK);

        JLabel plateLabel = new JLabel(plate, SwingConstants.CENTER);
        plateLabel.setFont(Theme.FONT_SMALL);
        plateLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        plateLabel.setForeground(Color.WHITE);

        java.text.NumberFormat format = java.text.NumberFormat.getCurrencyInstance(new java.util.Locale("id", "ID"));
        String formattedPrice = format.format(price).replace("Rp", "Rp ");
        JLabel priceLabel = new JLabel(formattedPrice + " /jam", SwingConstants.CENTER);
        priceLabel.setFont(Theme.FONT_REGULAR);
        priceLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        priceLabel.setBorder(BorderFactory.createEmptyBorder(4, 10, 4, 10));
        priceLabel.setOpaque(true);
        priceLabel.setBackground(new Color(0, 102, 204)); // Blue box
        priceLabel.setForeground(Color.WHITE);

        JLabel statusLabel = new JLabel(status.toUpperCase(), SwingConstants.CENTER);
        statusLabel.setFont(new Font("Segoe UI", Font.BOLD, 10));
        statusLabel.setOpaque(true);
        if (status.equalsIgnoreCase("available")) {
            statusLabel.setBackground(new Color(220, 255, 220));
            statusLabel.setForeground(new Color(0, 150, 0));
        } else {
            statusLabel.setBackground(new Color(255, 220, 220));
            statusLabel.setForeground(new Color(200, 0, 0));
        }
        statusLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        statusLabel.setBorder(BorderFactory.createEmptyBorder(2, 6, 2, 6));

        add(Box.createRigidArea(new Dimension(0, 10)));
        add(iconLabel);
        add(Box.createRigidArea(new Dimension(0, 5)));
        add(statusLabel);
        add(Box.createRigidArea(new Dimension(0, 5)));
        add(brandLabel);
        add(plateLabel);
        add(transLabel);
        add(priceLabel);
        add(Box.createRigidArea(new Dimension(0, 10)));

        setPreferredSize(new Dimension(240, 240));
    }
}
