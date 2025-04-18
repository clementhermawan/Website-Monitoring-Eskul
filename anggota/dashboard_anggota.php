<?php
session_start();
include '../config.php';

// Cek apakah pengguna sudah login dan memiliki role anggota
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anggota') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID anggota yang login
$user_id = $_SESSION['user_id'];

// Ambil data pengguna termasuk foto profil
$queryUser = "SELECT nama, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser) or die("Error Query User: " . mysqli_error($conn));
$user = mysqli_fetch_assoc($resultUser);

$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Ambil data eskul yang diikuti anggota
$queryEskul = "SELECT e.id, e.nama, e.deskripsi, e.prestasi 
               FROM eskul e
               JOIN users u ON u.eskul_id = e.id
               WHERE u.id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul) or die("Error Query Eskul: " . mysqli_error($conn));
$eskul = mysqli_fetch_assoc($resultEskul);

// Pastikan eskul ada sebelum mengambil kegiatan/program
$eskul_id = $eskul ? $eskul['id'] : 0;

// Ambil data kegiatan eskul
$queryKegiatan = "SELECT nama, deskripsi, tanggal FROM kegiatan WHERE eskul_id = $eskul_id ORDER BY tanggal DESC LIMIT 3";
$resultKegiatan = mysqli_query($conn, $queryKegiatan) or die("Error Query Kegiatan: " . mysqli_error($conn));

// Ambil data program eskul
$queryProgram = "SELECT nama, deskripsi, tanggal_mulai, tanggal_selesai 
                 FROM program 
                 WHERE eskul_id = $eskul_id 
                 ORDER BY tanggal_mulai DESC LIMIT 3";
$resultProgram = mysqli_query($conn, $queryProgram) or die("Error Query Program: " . mysqli_error($conn));
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f4f7f6;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background: #141e30;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 15px;
            display: block;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #243b55;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15);
        }
        /* Navbar */
        .navbar {
            background: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: calc(100% - 250px);
            top: 0;
            left: 250px;
            z-index: 1000;
        }
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .profile-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 150px;
            overflow: hidden;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
            transition: 0.3s;
        }
        .dropdown-menu a:hover {
            background: #f0f0f0;
        }
        .show {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Eskul</h3>
        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kegiatan.php"><i class="fas fa-calendar"></i> Kegiatan</a>
        <a href="program.php"><i class="fas fa-tasks"></i> Program</a>
        <a href="prestasi.php"><i class="fas fa-trophy"></i> Prestasi</a>
    </div>

    <!-- Navbar -->
    <div class="navbar">
        <h4>Dashboard Anggota</h4>
        <div class="profile-dropdown">
            <img src="../uploads/<?= htmlspecialchars($fotoProfil); ?>" alt="Profile" id="profileIcon">
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" style="margin-top: 70px;">
        <h2>Selamat Datang di Eskul <?= htmlspecialchars($eskul['nama']); ?>!</h2>
        <p><?= htmlspecialchars($eskul['deskripsi']); ?></p>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fas fa-calendar-check"></i> Kegiatan Mendatang</h5>
                    <ul>
                        <?php while ($kegiatan = mysqli_fetch_assoc($resultKegiatan)) : ?>
                            <li><strong><?= htmlspecialchars($kegiatan['nama']); ?></strong> - <?= date('d M Y', strtotime($kegiatan['tanggal'])); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fas fa-medal"></i> Prestasi Eskul</h5>
                    <p><?= nl2br(htmlspecialchars($eskul['prestasi'])); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fas fa-trophy"></i> Program Eskul</h5>
                    <ul>
                        <?php while ($program = mysqli_fetch_assoc($resultProgram)) : ?>
                            <li><strong><?= htmlspecialchars($program['nama']); ?></strong> (<?= date('d M Y', strtotime($program['tanggal_mulai'])); ?> - <?= date('d M Y', strtotime($program['tanggal_selesai'])); ?>)</li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('profileIcon').addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.toggle('show');
        });
    </script>

</body>
</html>
