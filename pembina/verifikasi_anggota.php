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

// Ambil informasi pembina dan nama eskul
$queryUser = "SELECT users.nama, users.email, users.foto, users.eskul_id, eskul.nama AS eskul_nama 
              FROM users 
              JOIN eskul ON users.eskul_id = eskul.id 
              WHERE users.id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$eskul_id = $user['eskul_id'];

// Proses verifikasi anggota
if (isset($_GET['id']) && isset($_GET['status'])) {
    $anggota_id = $_GET['id'];
    $status = $_GET['status'];

    if ($status === 'accept') {
        // Ubah status anggota menjadi 'aktif'
        $queryUpdate = "UPDATE users SET status = 'aktif' WHERE id = $anggota_id AND eskul_id = $eskul_id";
        mysqli_query($conn, $queryUpdate);
    } elseif ($status === 'reject') {
        // Hapus anggota dari database
        $queryDelete = "DELETE FROM users WHERE id = $anggota_id AND eskul_id = $eskul_id";
        mysqli_query($conn, $queryDelete);
    }

    // Redirect kembali ke halaman dashboard pembina
    header("Location: dashboard_pembina.php");
    exit();
}

// Ambil daftar anggota yang menunggu verifikasi
$queryVerifikasi = "SELECT users.id, users.nama, users.email, users.role, eskul.nama AS eskul_nama 
                    FROM users 
                    JOIN eskul ON users.eskul_id = eskul.id 
                    WHERE users.eskul_id = $eskul_id AND users.status = 'pending'";
$resultVerifikasi = mysqli_query($conn, $queryVerifikasi);

// Ambil daftar anggota aktif
$queryAnggota = "SELECT users.id, users.nama, users.email, users.role, eskul.nama AS eskul_nama 
                 FROM users 
                 JOIN eskul ON users.eskul_id = eskul.id 
                 WHERE users.eskul_id = $eskul_id AND users.status = 'aktif'";

$resultAnggota = mysqli_query($conn, $queryAnggota);
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
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <h2 class="text-center">Dashboard Pembina</h2>

        <h4>Daftar Anggota Menunggu Verifikasi</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Eskul</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultVerifikasi)) { ?>
                    <tr>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= ucfirst($row['role']); ?></td>
                        <td><?= $row['eskul_nama']; ?></td>
                        <td>
                            <a href="verifikasi_anggota.php?id=<?= $row['id']; ?>&status=accept" class="btn btn-success">Terima</a>
                            <a href="verifikasi_anggota.php?id=<?= $row['id']; ?>&status=reject" class="btn btn-danger">Tolak</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>