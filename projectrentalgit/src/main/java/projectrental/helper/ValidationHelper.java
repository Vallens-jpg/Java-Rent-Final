package projectrental.helper;

public class ValidationHelper {

    /**
     * Mengecek apakah ada string input yang kosong atau bernilai null.
     * @param fields Kumpulan string input yang ingin diperiksa
     * @return true jika salah satu input null, kosong, atau hanya berisi spasi
     */
    public static boolean isAnyEmpty(String... fields) {
        if (fields == null) {
            return true;
        }
        for (String field : fields) {
            if (field == null || field.trim().isEmpty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Memvalidasi format email sederhana (harus mengandung '@' dan '.').
     * @param email Email yang ingin divalidasi
     * @return true jika email valid, false jika tidak
     */
    public static boolean isValidEmail(String email) {
        if (email == null) {
            return false;
        }
        return email.contains("@") && email.contains(".");
    }
}
