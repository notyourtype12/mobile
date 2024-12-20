<?php
session_start();

// Menampilkan SweetAlert konfirmasi logout
echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Menghapus semua sesi jika konfirmasi logout
                    ". session_unset() .";
                    ". session_destroy() .";
                    window.location = '../index.php'; // Arahkan ke halaman login
                } else {
                    
                }
            });
        });
    </script>";
?>
