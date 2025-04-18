<?php
session_start();
include '../config.php';

// Cek apakah pengguna sudah login dan memiliki role anggota
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna termasuk foto profil
$queryUser = "SELECT nama, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser) or die("Error Query User: " . mysqli_error($conn));
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Ambil ekstrakurikuler yang diikuti oleh anggota
$queryEskul = "SELECT e.id, e.nama FROM eskul e JOIN users u ON u.eskul_id = e.id WHERE u.id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul) or die("Error Query Eskul: " . mysqli_error($conn));
$eskul = mysqli_fetch_assoc($resultEskul);
$eskul_id = $eskul ? $eskul['id'] : 0;

// Ambil daftar program berdasarkan ekstrakurikuler yang diikuti
$queryProgram = "SELECT nama, deskripsi, tanggal_mulai, tanggal_selesai FROM program WHERE eskul_id = $eskul_id ORDER BY tanggal_mulai DESC";
$resultProgram = mysqli_query($conn, $queryProgram) or die("Error Query Program: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program | Monitoring Eskul</title>
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
        /* Navbar Styling */
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
        .dropdown-menu {
            right: 0;
            left: auto;
        }
        /* Card Styling */
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">Anggota</h3>
    <a href="dashboard_anggota.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="kegiatan.php"><i class="fas fa-calendar-alt"></i> Kegiatan</a>
    <a href="program.php"><i class="fas fa-tasks"></i> Program</a>
    <a href="prestasi.php"><i class="fas fa-trophy"></i> Prestasi</a>
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
        <h2 class="text-center">Daftar Program - <?php echo $eskul['nama']; ?></h2>
        <div class="row mt-3">
            <?php while ($program = mysqli_fetch_assoc($resultProgram)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-3">
                        <h5 class="card-title"><?php echo $program['nama']; ?></h5>
                        <p class="card-text"><?php echo $program['deskripsi']; ?></p>
                        <small class="text-muted">Mulai: <?php echo date('d M Y', strtotime($program['tanggal_mulai'])); ?> | Selesai: <?php echo date('d M Y', strtotime($program['tanggal_selesai'])); ?></small>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
