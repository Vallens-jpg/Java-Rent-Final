package projectrental.helper;

import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

public class RentalHelperTest {

    @Test
    public void testFormatTimeDiff_ZeroSeconds() {
        assertEquals("00:00:00", RentalHelper.formatTimeDiff(0));
    }

    @Test
    public void testFormatTimeDiff_PositiveTime() {
        // 1 jam = 3600 detik
        assertEquals("01:00:00", RentalHelper.formatTimeDiff(3600));
        // 1 jam 1 menit 5 detik = 3665 detik
        assertEquals("01:01:05", RentalHelper.formatTimeDiff(3665));
        // 25 jam 30 menit 12 detik = 91812 detik
        assertEquals("25:30:12", RentalHelper.formatTimeDiff(91812));
    }

    @Test
    public void testFormatTimeDiff_NegativeTime() {
        // -1 jam = -3600 detik (terlambat)
        assertEquals("-01:00:00", RentalHelper.formatTimeDiff(-3600));
        // -1 jam 1 menit 5 detik = -3665 detik
        assertEquals("-01:01:05", RentalHelper.formatTimeDiff(-3665));
    }

    @Test
    public void testCalculateTotalPrice_NotOverdue() {
        double result = RentalHelper.calculateTotalPrice(150000.0, false, 25000.0);
        assertEquals(150000.0, result, 0.001);
    }

    @Test
    public void testCalculateTotalPrice_Overdue() {
        double result = RentalHelper.calculateTotalPrice(150000.0, true, 25000.0);
        assertEquals(175000.0, result, 0.001);
    }

    @Test
    public void testCalculateTotalPrice_ZeroValues() {
        double result = RentalHelper.calculateTotalPrice(0.0, true, 0.0);
        assertEquals(0.0, result, 0.001);
    }

    @Test
    public void testCalculateTotalPrice_NegativePriceThrowsException() {
        assertThrows(IllegalArgumentException.class, () -> {
            RentalHelper.calculateTotalPrice(-100000.0, false, 20000.0);
        });
    }

    @Test
    public void testCalculateTotalPrice_NegativePenaltyThrowsException() {
        assertThrows(IllegalArgumentException.class, () -> {
            RentalHelper.calculateTotalPrice(100000.0, true, -20000.0);
        });
    }
}
