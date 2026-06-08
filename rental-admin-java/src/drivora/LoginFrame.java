package drivora;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import org.mindrot.jbcrypt.BCrypt;

public class LoginFrame extends JFrame {

    private JTextField emailField;
    private JPasswordField passwordField;
    private JTextField phoneField;
    private JLabel phoneLabel;
    private JButton loginButton;
    private JButton switchBtn;
    private JLabel titleLabel;
    private boolean isLoginMode = false;

    public LoginFrame() {
        setTitle("Drivora Admin - Sign In");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        
        // Mengatur ukuran default window (sebelum di-maximize atau saat di-restore)
        setSize(700, 550);
        setLocationRelativeTo(null); // Memastikan muncul di tengah layar
        
        // Memaksa window untuk selalu Full Screen (Maksimal) saat pertama kali dibuka
        setExtendedState(JFrame.MAXIMIZED_BOTH);

        // Main background panel
        JPanel mainPanel = new JPanel();
        mainPanel.setBackground(Theme.PRIMARY_COLOR);
        mainPanel.setLayout(new GridBagLayout()); // To center the login card

        // Login Card Panel (Diperbesar)
        JPanel cardPanel = new JPanel();
        cardPanel.setBackground(Theme.CARD_COLOR);
        cardPanel.setPreferredSize(new Dimension(550, 450));
        cardPanel.setLayout(new GridBagLayout());
        cardPanel.setBorder(BorderFactory.createEmptyBorder(30, 40, 30, 40));

        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(15, 10, 15, 10);
        gbc.fill = GridBagConstraints.HORIZONTAL;

        // Title "Sign in"
        titleLabel = new JLabel("Sign in", SwingConstants.CENTER);
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 36)); // Font lebih besar
        titleLabel.setForeground(Theme.TEXT_DARK);
        gbc.gridx = 0;
        gbc.gridy = 0;
        gbc.gridwidth = 2;
        gbc.insets = new Insets(10, 10, 20, 10); // Jarak bawah title sedikit dikurangi
        cardPanel.add(titleLabel, gbc);

        // Reset insets untuk form
        gbc.insets = new Insets(10, 10, 10, 10);

        // --- FIELD EMAIL ---
        JLabel emailLabel = new JLabel("Email:");
        emailLabel.setFont(Theme.FONT_SUBHEADER);
        gbc.gridy = 1;
        gbc.gridwidth = 1;
        gbc.weightx = 0.2;
        cardPanel.add(emailLabel, gbc);

        emailField = new JTextField();
        emailField.setFont(Theme.FONT_SUBHEADER);
        emailField.setPreferredSize(new Dimension(300, 45)); // Memperbesar ukuran textfield
        gbc.gridx = 1;
        gbc.weightx = 0.8;
        cardPanel.add(emailField, gbc);

        // --- FIELD PASSWORD ---
        JLabel passwordLabel = new JLabel("Password:");
        passwordLabel.setFont(Theme.FONT_SUBHEADER);
        gbc.gridx = 0;
        gbc.gridy = 2;
        gbc.weightx = 0.2;
        cardPanel.add(passwordLabel, gbc);

        passwordField = new JPasswordField();
        passwordField.setFont(Theme.FONT_SUBHEADER);
        passwordField.setPreferredSize(new Dimension(300, 45)); // Memperbesar ukuran password
        gbc.gridx = 1;
        gbc.weightx = 0.8;
        cardPanel.add(passwordField, gbc);

        // --- FIELD NO TELP ---
        phoneLabel = new JLabel("No telp:");
        phoneLabel.setFont(Theme.FONT_SUBHEADER);
        gbc.gridx = 0;
        gbc.gridy = 3;
        gbc.weightx = 0.2;
        cardPanel.add(phoneLabel, gbc);

        phoneField = new JTextField();
        phoneField.setFont(Theme.FONT_SUBHEADER);
        phoneField.setPreferredSize(new Dimension(300, 45)); // Memperbesar ukuran textfield
        gbc.gridx = 1;
        gbc.weightx = 0.8;
        cardPanel.add(phoneField, gbc);

        // --- BUTTON SIGN IN ---
        loginButton = new JButton("Sign in");
        loginButton.setFont(Theme.FONT_SUBHEADER);
        loginButton.setBackground(Theme.CARD_COLOR); 
        loginButton.setForeground(Theme.TEXT_DARK);
        loginButton.setFocusPainted(false);
        loginButton.setPreferredSize(new Dimension(150, 45)); // Ukuran tombol diperbesar
        
        gbc.gridx = 0;
        gbc.gridy = 4;
        gbc.gridwidth = 2;
        gbc.insets = new Insets(20, 10, 5, 10); 
        
        // Memasukkan tombol ke dalam panel agar bisa di tengah
        JPanel buttonPanel = new JPanel();
        buttonPanel.setBackground(Theme.CARD_COLOR);
        buttonPanel.add(loginButton);
        cardPanel.add(buttonPanel, gbc);

        // --- SWITCH BUTTON (Ke Halaman Login) ---
        switchBtn = new JButton("Ke Halaman Login");
        switchBtn.setFont(Theme.FONT_REGULAR);
        switchBtn.setForeground(Color.BLUE);
        switchBtn.setBorderPainted(false);
        switchBtn.setContentAreaFilled(false);
        switchBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));
        switchBtn.setFocusPainted(false);
        
        gbc.gridy = 5;
        gbc.insets = new Insets(0, 10, 10, 10);
        JPanel switchPanel = new JPanel();
        switchPanel.setBackground(Theme.CARD_COLOR);
        switchPanel.add(switchBtn);
        cardPanel.add(switchPanel, gbc);

        // Action Listener for Login
        loginButton.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                performLogin();
            }
        });

        // Action Listener for Switch
        switchBtn.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                isLoginMode = !isLoginMode;
                if (isLoginMode) {
                    titleLabel.setText("LOGIN");
                    loginButton.setText("LOGIN");
                    phoneLabel.setVisible(false);
                    phoneField.setVisible(false);
                    switchBtn.setText("Ke Halaman Sign In");
                } else {
                    titleLabel.setText("Sign in");
                    loginButton.setText("Sign in");
                    phoneLabel.setVisible(true);
                    phoneField.setVisible(true);
                    switchBtn.setText("Ke Halaman Login");
                }
                // Refresh UI
                cardPanel.revalidate();
                cardPanel.repaint();
            }
        });

        mainPanel.add(cardPanel);
        add(mainPanel);
    }

    private void performLogin() {
        String email = emailField.getText().trim();
        String password = new String(passwordField.getPassword());
        String phone = phoneField.getText().trim(); // Sesuai desain

        if (email.isEmpty() || password.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Email dan Password tidak boleh kosong!", "Error", JOptionPane.ERROR_MESSAGE);
            return;
        }

        if (!isLoginMode) {
            // SIGN IN (REGISTER) LOGIC
            if (phone.isEmpty()) {
                JOptionPane.showMessageDialog(this, "Nomor telepon harus diisi untuk pendaftaran!", "Error", JOptionPane.ERROR_MESSAGE);
                return;
            }

            try (Connection conn = Database.getConnection()) {
                // Periksa apakah email sudah terdaftar
                String checkSql = "SELECT id FROM users WHERE email = ?";
                try (PreparedStatement checkStmt = conn.prepareStatement(checkSql)) {
                    checkStmt.setString(1, email);
                    ResultSet rs = checkStmt.executeQuery();
                    if (rs.next()) {
                        JOptionPane.showMessageDialog(this, "Email tersebut sudah terdaftar!", "Pendaftaran Gagal", JOptionPane.ERROR_MESSAGE);
                        return;
                    }
                }

                // Masukkan Admin baru
                String insertSql = "INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, ?, 'admin', ?, NOW(), NOW())";
                try (PreparedStatement insertStmt = conn.prepareStatement(insertSql)) {
                    String name = email.split("@")[0]; // Gunakan awalan email sebagai nama
                    String hashedPw = BCrypt.hashpw(password, BCrypt.gensalt());
                    
                    insertStmt.setString(1, name);
                    insertStmt.setString(2, email);
                    insertStmt.setString(3, hashedPw);
                    insertStmt.setString(4, phone);
                    
                    insertStmt.executeUpdate();
                    
                    JOptionPane.showMessageDialog(this, "Pendaftaran berhasil! Akun Admin Anda sudah jadi.\nSilakan login.", "Sukses", JOptionPane.INFORMATION_MESSAGE);
                    
                    // Kembalikan UI ke mode Login
                    isLoginMode = true;
                    titleLabel.setText("LOGIN");
                    loginButton.setText("LOGIN");
                    phoneLabel.setVisible(false);
                    phoneField.setVisible(false);
                    switchBtn.setText("Ke Halaman Sign In");
                    this.revalidate();
                    this.repaint();
                    passwordField.setText("");
                }
            } catch (Exception ex) {
                JOptionPane.showMessageDialog(this, "Koneksi Database Gagal:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
                ex.printStackTrace();
            }
            return;
        }

        // --- LOGIN LOGIC ---

        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT id, name, password FROM users WHERE email = ? AND role = 'admin'";
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                stmt.setString(1, email);
                ResultSet rs = stmt.executeQuery();
                
                if (rs.next()) {
                    int adminId = rs.getInt("id");
                    String adminName = rs.getString("name");
                    String dbHash = rs.getString("password");
                    
                    // Laravel uses $2y$ prefix for BCrypt, jBCrypt requires $2a$ prefix
                    String javaHash = dbHash.replace("$2y$", "$2a$");
                    
                    if (BCrypt.checkpw(password, javaHash)) {
                        // Berhasil login
                        JOptionPane.showMessageDialog(this, "Login Berhasil! Selamat datang " + adminName, "Sukses", JOptionPane.INFORMATION_MESSAGE);
                        
                        DashboardFrame dashboard = new DashboardFrame(adminId, adminName);
                        dashboard.setVisible(true);
                        this.dispose(); // Tutup window login
                    } else {
                        JOptionPane.showMessageDialog(this, "Password salah.", "Login Gagal", JOptionPane.ERROR_MESSAGE);
                    }
                } else {
                    JOptionPane.showMessageDialog(this, "Email tidak terdaftar atau Anda bukan Admin.", "Login Gagal", JOptionPane.ERROR_MESSAGE);
                }
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Koneksi Database Gagal:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }
}
