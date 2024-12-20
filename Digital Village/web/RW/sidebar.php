<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar With Bootstrap</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../admin/sidebar-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">Digital Village</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link">
                        <i class="bi bi-house-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link has-dropdown" data-bs-toggle="collapse" data-bs-target="#pengajuan-surat"
                        aria-expanded="false" aria-controls="pengajuan-surat">
                        <i class="bi bi-envelope-check-fill"></i>
                        <span>Pengajuan Surat</span>
                    </a>
                    <ul id="pengajuan-surat" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="suratmasuk.php" class="sidebar-link submenu-link">Surat Masuk</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="suratselesai.php" class="sidebar-link submenu-link">Surat Selesai</a>
                        </li>
                    </ul>
                </li>
               
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" onclick="confirmLogout(event)">
                       <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
    </div>

    <script>
        function confirmLogout(event) {
            event.preventDefault();

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
                    fetch('index.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'logout=true' })
                        .then(response => {
                            if (response.ok) {
                                window.location.href = 'index.php';
                            }
                        });
                }
            });
        }

        // Mendapatkan elemen-elemen sidebar
        const dropdownToggle = document.querySelector('.sidebar-link.has-dropdown');
        const dropdownMenu = document.querySelector('#auth');
        const sidebarLinks = document.querySelectorAll('.sidebar-link:not(.submenu-link)');

        // Toggle untuk dropdown
        dropdownToggle.addEventListener('click', (event) => {
            event.preventDefault();
            dropdownMenu.classList.toggle('show');
        });

        // Event listener untuk sidebar lainnya, agar menutup dropdown
        sidebarLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                if (!event.target.classList.contains('submenu-link')) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_start();
        session_unset();
        session_destroy();
        exit;
    }
    ?>
</body>

</html>