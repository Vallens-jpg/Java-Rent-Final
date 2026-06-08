package drivora;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class DashboardFrame extends JFrame {

    private JPanel cardsPanel;
    private CardLayout cardLayout;
    private JPanel katalogGrid;
    
    private OrderPanel orderPanel;
    private ReturnPanel returnPanel;
    private IncomeSheetPanel incomeSheetPanel;

    private int adminId;

    public DashboardFrame(int adminId, String adminName) {
        this.adminId = adminId;
        setTitle("Drivora Admin - Dashboard");
        setSize(1000, 700);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setExtendedState(JFrame.MAXIMIZED_BOTH);
        setLayout(new BorderLayout());

        // --- TOP BAR (Stacked Vertically) ---
        JPanel topBarContainer = new JPanel();
        topBarContainer.setLayout(new BoxLayout(topBarContainer, BoxLayout.Y_AXIS));
        topBarContainer.setBackground(Theme.PRIMARY_COLOR);
        topBarContainer.setBorder(BorderFactory.createEmptyBorder(15, 20, 15, 20));

        // Row 1: Title & Profile
        JPanel row1 = new JPanel(new BorderLayout());
        row1.setOpaque(false);
        
        JLabel titleLabel = new JLabel("HELLO, " + adminName.toUpperCase(), SwingConstants.CENTER);
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 32));
        titleLabel.setForeground(Theme.TEXT_DARK);
        
        JPanel rightProfilePanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        rightProfilePanel.setOpaque(false);
        
        JLabel profileLabel = new JLabel("👤 " + adminName.toUpperCase());
        profileLabel.setFont(Theme.FONT_SUBHEADER);
        profileLabel.setForeground(Theme.TEXT_DARK);
        profileLabel.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(Theme.TEXT_DARK, 1),
            BorderFactory.createEmptyBorder(5, 15, 5, 15)
        ));

        JButton logoutButton = new JButton("Logout");
        logoutButton.setFont(Theme.FONT_REGULAR);
        logoutButton.setBackground(Theme.BUTTON_DANGER);
        logoutButton.setForeground(Theme.TEXT_DARK);
        logoutButton.setFocusPainted(false);
        logoutButton.addActionListener(e -> {
            new LoginFrame().setVisible(true);
            this.dispose();
        });

        rightProfilePanel.add(profileLabel);
        rightProfilePanel.add(logoutButton);
        
        // Dummy left panel to perfectly center the title
        JPanel dummyLeft = new JPanel();
        dummyLeft.setOpaque(false);
        dummyLeft.setPreferredSize(new Dimension(250, 10)); // approximate width of rightProfilePanel

        row1.add(dummyLeft, BorderLayout.WEST);
        row1.add(titleLabel, BorderLayout.CENTER);
        row1.add(rightProfilePanel, BorderLayout.EAST);
        
        // Row 2: Search Bar
        JPanel row2 = new JPanel(new FlowLayout(FlowLayout.CENTER));
        row2.setOpaque(false);
        row2.setBorder(BorderFactory.createEmptyBorder(10, 0, 0, 0));
        
        JTextField searchField = new JTextField("Search: Masukkan ID, Merk Mobil, Ukuran, Transmisi", 40);
        searchField.setFont(Theme.FONT_REGULAR);
        searchField.setPreferredSize(new Dimension(400, 35));
        searchField.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(Color.GRAY, 1, true),
            BorderFactory.createEmptyBorder(5, 15, 5, 15)
        ));
        row2.add(searchField);

        topBarContainer.add(row1);
        topBarContainer.add(row2);
        
        add(topBarContainer, BorderLayout.NORTH);

        // --- SIDEBAR ---
        JPanel sidebar = new JPanel();
        sidebar.setLayout(new BoxLayout(sidebar, BoxLayout.Y_AXIS));
        sidebar.setBackground(Theme.SIDEBAR_COLOR);
        sidebar.setPreferredSize(new Dimension(220, 0));
        sidebar.setBorder(BorderFactory.createEmptyBorder(0, 0, 0, 0));

        orderPanel = new OrderPanel(adminId);
        returnPanel = new ReturnPanel(adminId);
        incomeSheetPanel = new IncomeSheetPanel();

        String[] menuItems = {"Katalog", "Order", "Return", "Income Sheet", "Master Data"};
        for (String item : menuItems) {
            JButton menuBtn = new JButton(item);
            menuBtn.setFont(Theme.FONT_SUBHEADER);
            menuBtn.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));
            menuBtn.setHorizontalAlignment(SwingConstants.LEFT);
            menuBtn.setBackground(Theme.SIDEBAR_COLOR);
            menuBtn.setForeground(Theme.TEXT_DARK);
            menuBtn.setFocusPainted(false);
            // Add border bottom for separation
            menuBtn.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 1, 0, Color.LIGHT_GRAY),
                BorderFactory.createEmptyBorder(10, 20, 10, 20)
            ));
            
            menuBtn.addActionListener(e -> {
                cardLayout.show(cardsPanel, item);
                if (item.equals("Katalog")) {
                    loadCarsFromDatabase("Semua");
                } else if (item.equals("Order")) {
                    orderPanel.loadPendingOrders();
                } else if (item.equals("Return")) {
                    returnPanel.loadActiveOrders();
                } else if (item.equals("Income Sheet")) {
                    incomeSheetPanel.loadData();
                }
            });
            
            sidebar.add(menuBtn);
        }
        add(sidebar, BorderLayout.WEST);

        // --- MAIN CONTENT AREA (CARD LAYOUT) ---
        cardLayout = new CardLayout();
        cardsPanel = new JPanel(cardLayout);
        cardsPanel.setBackground(Theme.PRIMARY_COLOR);

        // 1. KATALOG PANEL
        JPanel katalogPanelContainer = new JPanel(new BorderLayout());
        katalogPanelContainer.setBackground(Theme.PRIMARY_COLOR);
        
        // Filter dropdown right aligned
        JPanel filterPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        filterPanel.setOpaque(false);
        filterPanel.setBorder(BorderFactory.createEmptyBorder(5, 20, 5, 20));
        
        JLabel filterLabel = new JLabel("Filter Berdasarkan: ");
        filterLabel.setFont(Theme.FONT_REGULAR);
        filterLabel.setForeground(Theme.TEXT_DARK);
        
        String[] filterOptions = {"Semua", "Manual", "Automatic"};
        JComboBox<String> filterComboBox = new JComboBox<>(filterOptions);
        filterComboBox.setFont(Theme.FONT_REGULAR);
        filterComboBox.setBackground(Color.WHITE);
        
        // When filter is changed, reload data
        filterComboBox.addActionListener(e -> {
            String selected = (String) filterComboBox.getSelectedItem();
            loadCarsFromDatabase(selected);
        });

        filterPanel.add(filterLabel);
        filterPanel.add(filterComboBox);
        katalogPanelContainer.add(filterPanel, BorderLayout.NORTH);

        // Grid of cars
        katalogGrid = new JPanel(new GridLayout(0, 3, 25, 25)); // 3 columns
        katalogGrid.setBackground(Theme.PRIMARY_COLOR);
        katalogGrid.setBorder(new EmptyBorder(10, 30, 30, 30));

        JScrollPane scrollPane = new JScrollPane(katalogGrid);
        scrollPane.setBorder(null);
        scrollPane.getViewport().setBackground(Theme.PRIMARY_COLOR);
        
        // Increase scroll speed
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);
        
        katalogPanelContainer.add(scrollPane, BorderLayout.CENTER);

        cardsPanel.add(katalogPanelContainer, "Katalog");

        // 2. ORDER PANEL
        cardsPanel.add(orderPanel, "Order");

        // 3. RETURN PANEL
        cardsPanel.add(returnPanel, "Return");

        // 4. INCOME SHEET PANEL
        cardsPanel.add(incomeSheetPanel, "Income Sheet");

        // 5. MASTER DATA PANEL
        cardsPanel.add(new MasterDataPanel(), "Master Data");

        // Other dummy panels
        for (int i = 5; i < menuItems.length; i++) {
            JPanel panel = new JPanel(new GridBagLayout());
            panel.setBackground(Theme.PRIMARY_COLOR);
            JLabel label = new JLabel(menuItems[i] + " Content Area");
            label.setFont(Theme.FONT_HEADER);
            panel.add(label);
            cardsPanel.add(panel, menuItems[i]);
        }

        add(cardsPanel, BorderLayout.CENTER);

        // Initial load
        loadCarsFromDatabase("Semua");
    }

    private void loadCarsFromDatabase(String transmissionFilter) {
        try (Connection conn = Database.getConnection()) {
            String sql = "SELECT brand, transmission, image, price_per_hour, plate_number, status FROM cars WHERE 1=1";
            if (!transmissionFilter.equals("Semua")) {
                sql += " AND transmission = ?";
            }
            
            try (PreparedStatement stmt = conn.prepareStatement(sql)) {
                if (!transmissionFilter.equals("Semua")) {
                    stmt.setString(1, transmissionFilter);
                }
                
                ResultSet rs = stmt.executeQuery();
                katalogGrid.removeAll();
                
                while (rs.next()) {
                    String brand = rs.getString("brand");
                    String trans = rs.getString("transmission");
                    String image = rs.getString("image");
                    double price = rs.getDouble("price_per_hour");
                    String plate = rs.getString("plate_number");
                    String status = rs.getString("status");
                    
                    katalogGrid.add(new CarCard(brand, trans, image, price, plate, status));
                }
                
                katalogGrid.revalidate();
                katalogGrid.repaint();
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Gagal memuat katalog mobil:\n" + ex.getMessage(), "Error Database", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }
}
