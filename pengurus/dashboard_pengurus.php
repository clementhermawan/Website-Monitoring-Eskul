<?php
session_start();
include '../config.php';

// Cek apakah pengguna sudah login dan memiliki role pengurus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengurus') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil informasi pengurus
$queryUser = "SELECT nama, email, foto, eskul_id FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';
$eskul_id = $user['eskul_id'];

// Hitung jumlah kegiatan, program, dan anggota eskul
$queryTotalKegiatan = "SELECT COUNT(*) AS total FROM kegiatan WHERE eskul_id = $eskul_id";
$totalKegiatan = mysqli_fetch_assoc(mysqli_query($conn, $queryTotalKegiatan))['total'];

$queryTotalProgram = "SELECT COUNT(*) AS total FROM program WHERE eskul_id = $eskul_id";
$totalProgram = mysqli_fetch_assoc(mysqli_query($conn, $queryTotalProgram))['total'];

$queryTotalAnggota = "SELECT COUNT(*) AS total FROM users WHERE eskul_id = $eskul_id AND role = 'anggota'";
$totalAnggota = mysqli_fetch_assoc(mysqli_query($conn, $queryTotalAnggota))['total'];

// Ambil daftar kegiatan terbaru
$queryKegiatan = "SELECT * FROM kegiatan WHERE eskul_id = $eskul_id ORDER BY tanggal DESC LIMIT 5";
$resultKegiatan = mysqli_query($conn, $queryKegiatan);


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengurus | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f4f4f4;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #243b55;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #1b2a41;
        }
        /* Content */
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        /* Navbar */
        .navbar {
            background: #243b55;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .profile-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card i {
            font-size: 2rem;
            color: #243b55;
        }
        .dashboard-card h4 {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">Pengurus</h3>
    <a href="dashboard_pengurus.php"><i class="fas fa-home"></i> Dashboard</a>
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
                    <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container -->
    <div class="container mt-4">
        <h2 class="text-center">Dashboard Pengurus</h2>
        
        <div class="row mt-4">
            <!-- Total Kegiatan -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h4><?php echo $totalKegiatan; ?></h4>
                    <p>Total Kegiatan</p>
                </div>
            </div>
            <!-- Total Program -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fas fa-tasks"></i>
                    <h4><?php echo $totalProgram; ?></h4>
                    <p>Total Program</p>
                </div>
            </div>
            <!-- Total Anggota -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fas fa-users"></i>
                    <h4><?php echo $totalAnggota; ?></h4>
                    <p>Total Anggota</p>
                </div>
            </div>
        </div>

        <!-- Daftar Kegiatan Terbaru -->
        <div class="mt-5">
            <h4>Daftar Kegiatan Terbaru</h4>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($resultKegiatan)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['deskripsi']}</td>
                            <td>{$row['tanggal']}</td>
                        </tr>";
                        $no++;
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
