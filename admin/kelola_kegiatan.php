<?php
session_start();
include '../config.php';

// Cek apakah pengguna adalah pengurus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/// Ambil informasi pembina
$queryUser = "SELECT nama, email, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Set foto profil (default jika kosong)
$foto_profile = (!empty($userData['foto'])) ? "../uploads/" . $userData['foto'] : "../uploads/profile_default.png";

// Ambil Eskul Pengurus
$queryEskul = "SELECT eskul_id FROM users WHERE id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul);
$eskulData = mysqli_fetch_assoc($resultEskul);
$eskul_id = $eskulData['eskul_id'];

// Ambil daftar kegiatan eskul
$queryKegiatan = "SELECT * FROM kegiatan ORDER BY tanggal DESC";
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
            <h2 class="text-center">Kelola Kegiatan</h2>

            <!-- Tombol Tambah Kegiatan -->
            <div class="text-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan">
                    <i class="fas fa-plus"></i> Tambah Kegiatan
                </button>
            </div>

            <!-- Modal Tambah Kegiatan -->
            <div class="modal fade" id="modalTambahKegiatan" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Tambah Kegiatan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama Kegiatan</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" required>
                                </div>
                                <button type="submit" name="add_kegiatan" class="btn btn-primary w-100">Tambah</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal</label>
                                                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
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