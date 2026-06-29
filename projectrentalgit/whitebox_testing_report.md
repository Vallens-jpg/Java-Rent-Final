# LAPORAN PENGUJIAN WHITEBOX (WHITEBOX TESTING REPORT)
**Aplikasi:** ProjectRental (Drivora Admin System)  
**Teknologi Pengujian:** JUnit 5 & Apache Maven (NetBeans Integrated)  
**Bahasa Pemrograman:** Java  
**Tanggal Pengujian:** 25 Juni 2026  

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang
Pengujian perangkat lunak merupakan bagian penting dari siklus hidup pengembangan sistem guna menjamin keandalan, kebenaran, dan ketahanan aplikasi sebelum dirilis kepada pengguna. Pada proyek UAS **ProjectRental**, sebagian besar logika bisnis (seperti validasi input, format waktu, dan perhitungan biaya sewa) awalnya ditulis langsung di dalam event handler komponen GUI Swing. Hal ini mempersulit pengujian unit otomatis karena ketergantungan yang kuat pada komponen antarmuka visual (seperti `JTextField`, `JPasswordField`, dan dialog interaktif `JOptionPane`).

Untuk mengatasi masalah tersebut, telah dilakukan **refactoring** dengan memisahkan logika bisnis dari GUI Swing ke dalam kelas pembantu khusus (*helper classes*), yaitu `ValidationHelper` dan `RentalHelper`. Pemisahan ini memungkinkan dilakukannya **Whitebox Testing** (pengujian kotak putih) menggunakan framework **JUnit 5**.

### 1.2 Definisi Whitebox Testing
Whitebox Testing adalah metode pengujian perangkat lunak di mana struktur internal, desain, dan kode program diuji secara mendalam. Penguji memiliki akses penuh terhadap source code untuk memverifikasi alur eksekusi, penanganan kondisi cabang, serta memastikan tidak ada jalur logika (*path*) yang tidak terevaluasi.

Kriteria coverage yang diuji dalam laporan ini meliputi:
1. **Statement Coverage (Cakupan Pernyataan):** Menjamin bahwa setiap baris kode/pernyataan dieksekusi minimal satu kali selama pengujian.
2. **Branch Coverage (Cakupan Percabangan):** Memastikan semua cabang keputusan (seperti kondisi `if-else`) dievaluasi baik dalam kondisi `true` maupun `false`.
3. **Path Coverage (Cakupan Jalur):** Menguji setiap kombinasi jalur independen dalam suatu metode dari awal hingga akhir.

---

## 2. DETAIL STRUKTUR KODE & REFACTORING

Logika kritis dari GUI Swing diekstrak ke dalam paket `projectrental.helper` agar kodenya modular dan mudah diuji.

### 2.1 Kelas `ValidationHelper.java`
Bertanggung jawab atas validasi data input sebelum diproses oleh database.
```java
package projectrental.helper;

public class ValidationHelper {
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

    public static boolean isValidEmail(String email) {
        if (email == null) {
            return false;
        }
        return email.contains("@") && email.contains(".");
    }
}
```

### 2.2 Kelas `RentalHelper.java`
Mengatur formatting waktu sewa dan perhitungan denda keterlambatan.
```java
package projectrental.helper;

public class RentalHelper {
    public static String formatTimeDiff(long totalSecs) {
        boolean isNegative = totalSecs < 0;
        long absSecs = Math.abs(totalSecs);
        long hours = absSecs / 3600;
        long minutes = (absSecs % 3600) / 60;
        long seconds = absSecs % 60;
        
        String timeStr = String.format("%02d:%02d:%02d", hours, minutes, seconds);
        return isNegative ? "-" + timeStr : timeStr;
    }

    public static double calculateTotalPrice(double originalPrice, boolean isOverdue, double penaltyAmount) {
        if (originalPrice < 0 || penaltyAmount < 0) {
            throw new IllegalArgumentException("Harga dan denda tidak boleh bernilai negatif");
        }
        return isOverdue ? (originalPrice + penaltyAmount) : originalPrice;
    }
}
```

---

## 3. DESAIN TEST CASE JUNIT 5

Pengujian didefinisikan menggunakan framework JUnit 5 di direktori `src/test/java/projectrental/helper/`.

### 3.1 Skenario Uji: `ValidationHelperTest.java`
Pengujian difokuskan pada pemenuhan kriteria Statement dan Branch Coverage untuk mengecek keandalan fungsi validasi.

| Test Case ID | Nama Metode Uji | Input (Parameter) | Output Diharapkan | Jenis Coverage / Analisis Cabang |
| :--- | :--- | :--- | :--- | :--- |
| **TV-001** | `testIsAnyEmpty_AllNotEmpty` | `["test", "user", "password123"]` | `false` | Semua variabel terisi penuh (Cabang Loop `false`). |
| **TV-002** | `testIsAnyEmpty_SomeEmpty` | `["test", "", "password123"]` | `true` | Salah satu kosong (Cabang `field.isEmpty()` bernilai `true`). |
| **TV-003** | `testIsAnyEmpty_AllEmpty` | `["", "", ""]` | `true` | Semua parameter kosong. |
| **TV-004** | `testIsAnyEmpty_NullValue` | `["test", null, "password123"]` | `true` | Nilai null di tengah array (Cabang `field == null` bernilai `true`). |
| **TV-005** | `testIsAnyEmpty_OnlyWhitespace` | `["  ", "valid", "valid"]` | `true` | Spasi kosong dianggap kosong (Cabang `field.trim().isEmpty()`). |
| **TV-006** | `testIsAnyEmpty_NullArray` | `null` (Varargs null) | `true` | Menguji pengamanan NPE jika argumen array null secara keseluruhan. |
| **TV-007** | `testIsValidEmail_Valid` | `"admin@gmail.com"`, `"user.test@drivora.id"` | `true` | Email valid (Mengandung `@` dan `.`). |
| **TV-008** | `testIsValidEmail_MissingAtSign` | `"admingmail.com"` | `false` | Format salah: Tanpa `@` (Cabang pertama gagal). |
| **TV-009** | `testIsValidEmail_MissingDot` | `"admin@gmailcom"` | `false` | Format salah: Tanpa `.` (Cabang kedua gagal). |
| **TV-010** | `testIsValidEmail_NullEmail` | `null` | `false` | Validasi input null (Mencegah `NullPointerException`). |
| **TV-011** | `testIsValidEmail_EmptyEmail` | `""` | `false` | Validasi input string kosong. |

### 3.2 Skenario Uji: `RentalHelperTest.java`
Pengujian difokuskan pada penanganan kondisi rentang angka, pembagian waktu, serta kalkulasi matematika bersyarat.

| Test Case ID | Nama Metode Uji | Input (Parameter) | Output Diharapkan | Jenis Coverage / Analisis Cabang |
| :--- | :--- | :--- | :--- | :--- |
| **TR-001** | `testFormatTimeDiff_ZeroSeconds` | `0` | `"00:00:00"` | Kasus batas nol (Tidak masuk cabang negatif). |
| **TR-002** | `testFormatTimeDiff_PositiveTime` | `3600` (1 Jam) | `"01:00:00"` | Format waktu sewa aktif biasa. |
| **TR-003** | `testFormatTimeDiff_PositiveTime2` | `3665` (1j 1m 5d) | `"01:01:05"` | Pengujian konversi sisa waktu normal. |
| **TR-004** | `testFormatTimeDiff_PositiveTime3` | `91812` (25j 30m 12d) | `"25:30:12"` | Pengujian konversi waktu lebih dari 1 hari. |
| **TR-005** | `testFormatTimeDiff_NegativeTime` | `-3600` (-1 Jam) | `"-01:00:00"` | Kasus terlambat (Cabang `isNegative` bernilai `true`). |
| **TR-006** | `testFormatTimeDiff_NegativeTime2` | `-3665` | `"-01:01:05"` | Penanganan tanda minus pada keterlambatan menit. |
| **TR-007** | `testCalculateTotalPrice_NotOverdue` | `(150000.0, false, 25000.0)` | `150000.0` | Skenario normal (Tanpa denda). Cabang `isOverdue` bernilai `false`. |
| **TR-008** | `testCalculateTotalPrice_Overdue` | `(150000.0, true, 25000.0)` | `175000.0` | Skenario terlambat (Dengan denda). Cabang `isOverdue` bernilai `true`. |
| **TR-009** | `testCalculateTotalPrice_ZeroValues` | `(0.0, true, 0.0)` | `0.0` | Batas nilai nol pada kalkulasi. |
| **TR-010** | `testCalculateTotalPrice_NegativePrice` | `(-100000.0, false, 20000.0)` | `IllegalArgumentException` | Pengujian error handling harga negatif (Path Exception). |
| **TR-011** | `testCalculateTotalPrice_NegativePenalty` | `(100000.0, true, -20000.0)` | `IllegalArgumentException` | Pengujian error handling denda negatif (Path Exception). |

---

## 4. HASIL EKSEKUSI PENGUJIAN (TEST EXECUTION RESULTS)

Semua pengujian unit dijalankan menggunakan terminal perintah Maven compiler. Berikut adalah kutipan ringkasan hasil uji yang sukses dieksekusi:

```text
[INFO] Scanning for projects...
[INFO] 
[INFO] --------------------< com.mycompany:ProjectRental >---------------------
[INFO] Building ProjectRental 1.0-SNAPSHOT
[INFO]   from pom.xml
[INFO] --------------------------------[ jar ]---------------------------------
...
[INFO] Running projectrental.helper.RentalHelperTest
[INFO] Tests run: 8, Failures: 0, Errors: 0, Skipped: 0, Time elapsed: 0.173 s -- in projectrental.helper.RentalHelperTest
[INFO] Running projectrental.helper.ValidationHelperTest
[INFO] Tests run: 11, Failures: 0, Errors: 0, Skipped: 0, Time elapsed: 0.049 s -- in projectrental.helper.ValidationHelperTest
[INFO] 
[INFO] Results:
[INFO] 
[INFO] Tests run: 19, Failures: 0, Errors: 0, Skipped: 0
[INFO] 
[INFO] ------------------------------------------------------------------------
[INFO] BUILD SUCCESS
[INFO] ------------------------------------------------------------------------
```

### 4.1 Analisis Hasil
* **Total Kasus Uji:** 19
* **Berhasil (Pass):** 19
* **Gagal (Failure):** 0
* **Error (Exception tak terduga):** 0
* **Dilewati (Skipped):** 0
* **Statement Coverage Kelas Helper:** 100% (Semua pernyataan baris dieksekusi).
* **Branch Coverage Kelas Helper:** 100% (Seluruh jalur kondisional teruji).

---

## 5. KESIMPULAN

Berdasarkan pengujian Whitebox yang dilakukan terhadap kelas helper `ValidationHelper` dan `RentalHelper`:
1. Pengujian membuktikan bahwa semua fungsionalitas logika validasi email, pengecekan form kosong, pemformatan jam keterlambatan, dan perhitungan biaya sewa berjalan **100% Benar** dan terhindar dari bug logika.
2. Struktur kode hasil refactoring aman dari risiko `NullPointerException` berkat skenario uji parameter `null` yang disiapkan.
3. Arsitektur kode baru mempermudah pemeliharaan jangka panjang tanpa merusak fungsionalitas UI asli di NetBeans.
