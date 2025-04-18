<?php
session_start();
include '../config.php';

// Cek apakah pengguna adalah pengurus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user untuk foto profil
$queryUser = "SELECT foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$userData = mysqli_fetch_assoc($resultUser);

// Set foto profil (default jika kosong)
$foto_profile = (!empty($userData['foto'])) ? "../uploads/" . $userData['foto'] : "../uploads/profile_default.png";

// Ambil Eskul Pengurus
$queryEskul = "SELECT eskul_id FROM users WHERE id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul);
$eskulData = mysqli_fetch_assoc($resultEskul);
$eskul_id = $eskulData['eskul_id'];

// Ambil daftar kegiatan eskul
$queryKegiatan = "SELECT * FROM kegiatan WHERE eskul_id = $eskul_id ORDER BY tanggal DESC";
$resultKegiatan = mysqli_query($conn, $queryKegiatan);

// Tambah Kegiatan
if (isset($_POST['add_kegiatan'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];

    $insert = "INSERT INTO kegiatan (eskul_id, nama, deskripsi, tanggal) VALUES ($eskul_id, '$nama', '$deskripsi', '$tanggal')";
    mysqli_query($conn, $insert);
    header("Location: kelola_kegiatan.php");
}

// Edit Kegiatan
if (isset($_POST['edit_kegiatan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];

    $update = "UPDATE kegiatan SET nama='$nama', deskripsi='$deskripsi', tanggal='$tanggal' WHERE id=$id";
    mysqli_query($conn, $update);
    header("Location: kelola_kegiatan.php");
}

// Hapus Kegiatan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = "DELETE FROM kegiatan WHERE id=$id";
    mysqli_query($conn, $delete);
    header("Location: kelola_kegiatan.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kegiatan | Monitoring Eskul</title>
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
        .navbar {
            background: #243b55;
        }
        .navbar .nav-link {
            color: white !important;
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
    <h3 class="text-center">Pengurus</h3>
    <a href="dashboard_pengurus.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="kelola_kegiatan.php"><i class="fas fa-calendar-alt"></i> Kelola Kegiatan</a>
    <a href="kelola_program.php"><i class="fas fa-tasks"></i> Kelola Program</a>
    <a href="kelola_anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
</div>

<!-- Content -->
<div class="content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand">Monitoring Eskul</a>
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <img src="<?= $foto_profile; ?>" alt="Profile" class="profile-img"> Profil
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="edit_profile.php">Edit Profil</a></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Kelola Kegiatan</h2>

        <!-- Tabel Kegiatan -->
        <div class="table-container">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultKegiatan)) { ?>
                    <tr>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['deskripsi']; ?></td>
                        <td><?= $row['tanggal']; ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKegiatan<?= $row['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit Kegiatan -->
                    <div class="modal fade" id="modalEditKegiatan<?= $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title">Edit Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kegiatan</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" required><?= $row['deskripsi']; ?></textarea>
                                        </div>
                                        <button type="submit" name="edit_kegiatan" class="btn btn-warning w-100">Simpan Perubahan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
