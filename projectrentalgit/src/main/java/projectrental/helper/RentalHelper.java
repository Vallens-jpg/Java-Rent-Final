package projectrental.helper;

public class RentalHelper {

    /**
     * Memformat selisih waktu dalam detik menjadi string HH:MM:SS.
     * Jika totalSecs bernilai negatif, string akan diawali dengan tanda minus (-).
     * @param totalSecs Selisih waktu dalam detik
     * @return String hasil format HH:MM:SS atau -HH:MM:SS
     */
    public static String formatTimeDiff(long totalSecs) {
        boolean isNegative = totalSecs < 0;
        long absSecs = Math.abs(totalSecs);
        long hours = absSecs / 3600;
        long minutes = (absSecs % 3600) / 60;
        long seconds = absSecs % 60;
        
        String timeStr = String.format("%02d:%02d:%02d", hours, minutes, seconds);
        return isNegative ? "-" + timeStr : timeStr;
    }

    /**
     * Menghitung total harga sewa akhir. Jika masa sewa lewat dari waktu kembali (overdue),
     * maka denda akan ditambahkan ke harga sewa original.
     * @param originalPrice Harga sewa dasar mobil
     * @param isOverdue Status keterlambatan pengembalian
     * @param penaltyAmount Jumlah biaya denda
     * @return Total harga akhir yang harus dibayarkan
     */
    public static double calculateTotalPrice(double originalPrice, boolean isOverdue, double penaltyAmount) {
        if (originalPrice < 0 || penaltyAmount < 0) {
            throw new IllegalArgumentException("Harga dan denda tidak boleh bernilai negatif");
        }
        return isOverdue ? (originalPrice + penaltyAmount) : originalPrice;
    }
}
