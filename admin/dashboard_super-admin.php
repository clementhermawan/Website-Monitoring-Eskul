<?php
session_start();
include '../config.php';

// Pastikan hanya Super Admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$queryUser = "SELECT nama, email, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser) or die("Error Query User: " . mysqli_error($conn));
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Ambil data jumlah pengguna, eskul, kegiatan, dan program
$queryTotalUsers = "SELECT COUNT(*) AS total FROM users";
$resultUsers = mysqli_query($conn, $queryTotalUsers);
$totalUsers = mysqli_fetch_assoc($resultUsers)['total'];

$queryTotalEskul = "SELECT COUNT(*) AS total FROM eskul";
$resultEskul = mysqli_query($conn, $queryTotalEskul);
$totalEskul = mysqli_fetch_assoc($resultEskul)['total'];

$queryTotalKegiatan = "SELECT COUNT(*) AS total FROM kegiatan";
$resultKegiatan = mysqli_query($conn, $queryTotalKegiatan);
$totalKegiatan = mysqli_fetch_assoc($resultKegiatan)['total'];

$queryTotalProgram = "SELECT COUNT(*) AS total FROM program";
$resultProgram = mysqli_query($conn, $queryTotalProgram);
$totalProgram = mysqli_fetch_assoc($resultProgram)['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            height: 100vh;
            position: fixed;
            background: #243b55;
            color: white;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar a:hover {
            background: #1b2a41;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c3e50;
            padding: 12px 20px;
            color: white;
            border-radius: 8px;
            position: relative;
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            display: flex;
            align-items: center;
        }

        .profile-dropdown span {
            font-size: 16px;
            margin-right: 10px;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
        }

        .dropdown-menu {
            background: #fff;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            color: #333;
            font-size: 14px;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: #f1f1f1;
        }

        /* Content */
        .content {
            margin-left: 230px;
            padding: 20px;
        }

        /* Table Styles */
        .table {
            width: 95%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .table th {
            background: #243b55;
            color: white;
            text-transform: uppercase;
            padding: 10px;
            text-align: center;
        }

        .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .table tbody tr:hover {
            background: #f1f1f1;
        }

        /* Button Styling */
        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            text-decoration: none;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }

            .table {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }

            .table th,
            .table td {
                font-size: 14px;
                padding: 6px;
            }

            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .profile-dropdown {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Super-Admin</h3>
        <a href="dashboard_super-admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kelola_anggota.php"><i class="fas fa-users"></i> Kelola Users</a>
        <a href="kelola_program.php"><i class="fas fa-users"></i> Kelola Program</a>
        <a href="kelola_kegiatan.php"><i class="fas fa-calendar-alt"></i> Kelola Kegiatan</a>
        <a href="kelola_eskul.php"><i class="fas fa-calendar-alt"></i> Kelola eskul</a>
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

        <!-- Main Content -->
        <div class="content" style="margin-top: 70px;">
            <h2>Selamat Datang, Super Admin!</h2>
            <p>Kelola sistem monitoring ekstrakurikuler dengan mudah.</p>

            <div class="row">
                <div class="col-md-3">
                    <div class="card p-3">
                        <h5><i class="fas fa-users"></i> Total Pengguna</h5>
                        <p><?= $totalUsers; ?> Pengguna</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3">
                        <h5><i class="fas fa-school"></i> Total Eskul</h5>
                        <p><?= $totalEskul; ?> Eskul</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3">
                        <h5><i class="fas fa-calendar"></i> Total Kegiatan</h5>
                        <p><?= $totalKegiatan; ?> Kegiatan</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3">
                        <h5><i class="fas fa-tasks"></i> Total Program</h5>
                        <p><?= $totalProgram; ?> Program</p>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>