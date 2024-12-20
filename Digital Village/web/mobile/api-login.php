<?php
include 'koneksi.php';

$nik = isset($_POST['nik']) ? $_POST['nik'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if (!empty($nik) && !empty($password)) {
    // Menggunakan prepared statement
    $stmt = $conn->prepare("SELECT nik, password, no_hp FROM master_akun WHERE nik = ?");
    if ($stmt) {
        $stmt->bind_param("s", $nik);  // Only bind NIK for the query
        $stmt->execute();
        $stmt->store_result();
        
        // Check if the NIK exists
        if ($stmt->num_rows > 0) {
            // Fetch the result to get password and no_hp
            $stmt->bind_result($db_nik, $db_password, $db_no_hp);
            $stmt->fetch();

            // Verify password
            if ($password === $db_password) {
                // Check if no_hp is empty (account not activated)
                if (empty($db_no_hp)) {
                    echo "Akun belum diaktivasi. Silakan aktivasi akun terlebih dahulu.";
                } else {
                    echo "Selamat Datang";  // Login success
                }
            } else {
                echo "Password salah";  // Password mismatch
            }
        } else {
            echo "Silahkan Melakukan Aktivasi Akun Terlebih Dahulu";  // No such NIK
        }
        $stmt->close();
    } else {
        error_log("Database query failed: " . $conn->error);
        echo "Terjadi kesalahan pada query database.";
    }
} else {
    echo "Ada Data Yang Masih Kosong"; // If NIK or password is empty
}
?>
