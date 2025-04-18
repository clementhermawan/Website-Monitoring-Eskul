<?php
session_start();
include '../config.php';

// Cek apakah pengguna adalah pengurus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil informasi pembina
$queryUser = "SELECT nama, email, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Ambil Eskul Pengurus
$queryEskul = "SELECT eskul_id FROM users WHERE id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul);
$eskulData = mysqli_fetch_assoc($resultEskul);
$eskul_id = $eskulData['eskul_id'];

// Ambil daftar program eskul
// Ambil semua daftar program tanpa filter eskul
$queryProgram = "SELECT * FROM program ORDER BY tanggal_mulai DESC";
$resultProgram = mysqli_query($conn, $queryProgram);

// Tambah Program
if (isset($_POST['add_program'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    $insert = "INSERT INTO program (eskul_id, nama, deskripsi, tanggal_mulai, tanggal_selesai) 
               VALUES ('$eskul_id', '$nama', '$deskripsi', '$tanggal_mulai', '$tanggal_selesai')";
    if (mysqli_query($conn, $insert)) {
        header("Location: kelola_program.php");
        exit();
    }
}

// Edit Program
if (isset($_POST['edit_program'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    $update = "UPDATE program SET nama='$nama', deskripsi='$deskripsi', 
               tanggal_mulai='$tanggal_mulai', tanggal_selesai='$tanggal_selesai' WHERE id='$id'";
    if (mysqli_query($conn, $update)) {
        header("Location: kelola_program.php");
        exit();
    }
}

// Hapus Program
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = "DELETE FROM program WHERE id='$id'";
    if (mysqli_query($conn, $delete)) {
        header("Location: kelola_program.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Program | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: #f4f4f4;
        }

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
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #1b2a41;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
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

        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #ff8c00;
            border: none;
        }

        .btn-primary:hover {
            background: #ff6a00;
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

        <div class="container mt-4">
            <h2 class="text-center">Kelola Program</h2>

            <!-- Tombol Tambah -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Program
            </button>

            <!-- Modal Tambah Program -->
            <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Tambah Program</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="kelola_program.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama Program</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" required>
                                </div>
                                <button type="submit" name="add_program" class="btn btn-primary w-100">Tambah</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Program -->
            <div class="table-container">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($resultProgram)) { ?>
                            <tr>
                                <td><?= $row['nama']; ?></td>
                                <td><?= $row['deskripsi']; ?></td>
                                <td><?= $row['tanggal_mulai']; ?></td>
                                <td><?= $row['tanggal_selesai']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit Program -->
                            <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Edit Program</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="kelola_program.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Program</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" required><?= $row['deskripsi']; ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Mulai</label>
                                                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $row['tanggal_mulai']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Selesai</label>
                                                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= $row['tanggal_selesai']; ?>" required>
                                                </div>
                                                <button type="submit" name="edit_program" class="btn btn-warning w-100">Simpan Perubahan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </tbody>
                </table>
            </div>


</body>

</html>