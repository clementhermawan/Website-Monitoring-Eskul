<?php
session_start();
include '../config.php';

// Cek apakah pengguna sudah login dan memiliki role pembina
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembina') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil informasi pembina
$queryUser = "SELECT nama, email, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Ambil eskul yang dibina oleh pembina
$queryEskul = "SELECT * FROM eskul";
$resultEskul = mysqli_query($conn, $queryEskul);

// Ambil daftar anggota berdasarkan status
$queryAnggotaPending = "SELECT * FROM users WHERE status = 'pending'";
$resultAnggotaPending = mysqli_query($conn, $queryAnggotaPending);

$queryAnggotaAktif = "SELECT * FROM users WHERE status = 'aktif'";
$resultAnggotaAktif = mysqli_query($conn, $queryAnggotaAktif);

$queryAnggotaNonaktif = "SELECT * FROM users WHERE status = 'nonaktif'";
$resultAnggotaNonaktif = mysqli_query($conn, $queryAnggotaNonaktif);

// Ambil daftar kegiatan dan program
$queryKegiatan = "SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5";
$resultKegiatan = mysqli_query($conn, $queryKegiatan);

$queryProgram = "SELECT * FROM program ORDER BY tanggal_mulai DESC LIMIT 5";
$resultProgram = mysqli_query($conn, $queryProgram);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembina | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #243b55;
            color: white;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar h3 {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #1b2a41;
            padding-left: 20px;
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c3e50;
            padding: 15px 20px;
            color: white;
            border-radius: 10px;
        }

        .navbar-brand {
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .profile-dropdown {
            display: flex;
            align-items: center;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            border: 2px solid white;
        }

        /* Tables */
        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background: #34495e;
            color: white;
        }

        .table tbody tr:hover {
            background: #f1f1f1;
        }

        /* Buttons */
        .btn {
            transition: 0.3s ease;
        }

        .btn-success {
            background: #27ae60;
            border: none;
        }

        .btn-success:hover {
            background: #218c53;
        }

        .btn-danger {
            background: #e74c3c;
            border: none;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        /* Card Boxes */
        .card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.03);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
                padding: 15px;
            }

            .navbar {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Pembina</h3>
        <a href="dashboard_pembina.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="verifikasi_anggota.php"><i class="fas fa-user-check"></i> Verifikasi Anggota</a>
        <a href="kelola_kegiatan.php"><i class="fas fa-calendar-alt"></i> Kelola Kegiatan</a>
        <a href="kelola_program.php"><i class="fas fa-tasks"></i> Kelola Program</a>
        <a href="kelola_anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Navbar -->
        <nav class="navbar">
            <a class="navbar-brand" href="#">Monitoring Eskul</a>
            <div class="profile-dropdown">
                <span class="text-white"><?php echo $user['nama']; ?></span>
                <div class="dropdown">
                    <a href="#" class="text-white text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="../uploads/<?php echo $fotoProfil; ?>" alt="Foto Profil" class="profile-img">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profil</a></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="confirmLogout()">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <h2 class="text-center mb-4">Dashboard Pembina</h2>

            <!-- Eskul yang Dibina -->
            <div class="card p-3 mb-4">
                <h4 class="mb-3">Ekstrakurikuler yang Dibina</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nama Eskul</th>
                                <th>Deskripsi</th>
                                <th>Prestasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($eskul = mysqli_fetch_assoc($resultEskul)) { ?>
                                <tr>
                                    <td><?= $eskul['nama']; ?></td>
                                    <td><?= $eskul['deskripsi']; ?></td>
                                    <td><?= $eskul['prestasi']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daftar Anggota -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5 class="mb-3">Menunggu Verifikasi</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultAnggotaPending)) { ?>
                                        <tr>
                                            <td><?= $row['nama']; ?></td>
                                            <td><?= $row['email']; ?></td>
                                            <td>
                                                <a href="verifikasi_anggota.php?id=<?= $row['id']; ?>&status=accept" class="btn btn-success btn-sm">Terima</a>
                                                <a href="verifikasi_anggota.php?id=<?= $row['id']; ?>&status=reject" class="btn btn-danger btn-sm">Tolak</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5 class="mb-3">Aktif</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultAnggotaAktif)) { ?>
                                        <tr>
                                            <td><?= $row['nama']; ?></td>
                                            <td><?= $row['email']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5 class="mb-3">Nonaktif</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultAnggotaNonaktif)) { ?>
                                        <tr>
                                            <td><?= $row['nama']; ?></td>
                                            <td><?= $row['email']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Kegiatan -->
            <div class="card p-3 mb-4">
                <h4 class="mb-3">Daftar Kegiatan Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Deskripsi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($resultKegiatan)) { ?>
                                <tr>
                                    <td><?= $row['nama']; ?></td>
                                    <td><?= $row['deskripsi']; ?></td>
                                    <td><?= $row['tanggal']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daftar Program -->
            <div class="card p-3 mb-4">
                <h4 class="mb-3">Daftar Program Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nama Program</th>
                                <th>Deskripsi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($resultProgram)) { ?>
                                <tr>
                                    <td><?= $row['nama']; ?></td>
                                    <td><?= $row['deskripsi']; ?></td>
                                    <td><?= $row['tanggal_mulai']; ?> - <?= $row['tanggal_selesai']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function confirmLogout() {
                if (confirm("Apakah Anda yakin ingin logout?")) {
                    window.location.href = "../logout.php";
                }
            }
        </script>
</body>

</html>