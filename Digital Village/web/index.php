<?php
include 'koneksi.php'; // Database connection setup
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $nik = $_POST['NIK'];
        $password = $_POST['password'];

        // Query the database
        $sql = "SELECT * FROM master_akun WHERE NIK = '$nik' AND password = '$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            // Fetch the user data
            $row = mysqli_fetch_assoc($result);
            $level = $row['level'];

            // Login berhasil, simpan NIK di sesi
            $_SESSION['NIK'] = $nik;

            // Redirect based on user level
            if ($level == 3) {
                // Redirect to RT (level 3)
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login berhasil!',
                            showConfirmButton: false, // Menghilangkan tombol
                            timer: 1500
                        }).then(() => {
                                window.location = 'rt/index.php'; // Redirect ke RT dashboard
                        });
                    });
                </script>";
            } elseif ($level == 2) {
                // Redirect to RW (level 2)
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login berhasil!',
                            showConfirmButton: false, // Menghilangkan tombol
                            timer: 1500
                        }).then(() => {
                                window.location = 'rw/index.php'; // Redirect ke RW dashboard
                        });
                    });
                </script>";
            }  elseif ($level == 1) {
                // Redirect to RW (level 1)
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login berhasil!',
                            showConfirmButton: false, // Menghilangkan tombol
                            timer: 1500
                        }).then(() => {
                                window.location = 'admin/index.php'; // Redirect ke RW dashboard
                        });
                    });
                </script>";
            } else {
                // For other levels, handle accordingly (or error message)
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: 'Access not authorized',
                            showConfirmButton: false, // Menghilangkan tombol
                            timer: 1500
                        });
                    });
                </script>";
            }
        } else {
            // Login failed
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: 'NIK atau Password Salah',
                        showConfirmButton: false, // Menghilangkan tombol
                        timer: 1500
                    });
                });
            </script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <form action="index.php" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="NIK" placeholder="NIK" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
            <!-- <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div> -->
        </form>
    </div>
</body>
</html>
