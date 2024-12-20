<?php
include 'koneksi.php'; // Database connection setup
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Registration logic
        $nik = $_POST['NIK'];
        $telepon = $_POST['telepon']; 
        $password = $_POST['password']; // Consider using password_hash for security
        
        // Insert into the database
        $sql = "INSERT INTO master_akun (nik, no_hp, password) VALUES ('$nik', '$telepon', '$password')";
        if (mysqli_query($conn, $sql)) {
            // Use JavaScript to trigger SweetAlert on successful registration
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration successful!',
                        text: 'You have successfully registered.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'index.php'; // Redirect to login page after success
                        }
                    });
                });
            </script>";
        } else {
            echo "Registration failed: " . mysqli_error($conn);
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 script included -->
</head>
<body>
  <div class="wrapper">
    <form action="register.php" method="POST"> <!-- Use explicit action -->
      <h1>Register</h1>
      <div class="input-box">
        <input type="text" name="NIK" placeholder="NIK" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <input type="text" name="telepon" placeholder="Telephone" required>
        <i class='bx bxs-phone'></i>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt'></i>
      </div>
      <div class="remember-forgot">
        <label><input type="checkbox">Remember Me</label>
      </div>
      <button type="submit" name="register" class="btn">Register</button> <!-- Submit button with name attribute -->
      <div class="register-link">
        <p>Already have an account? <a href="index.php">Login</a></p>
      </div>
    </form>
  </div>
</body>
</html>