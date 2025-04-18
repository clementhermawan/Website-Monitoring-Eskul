<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$queryUser = "SELECT nama, email, foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$fotoProfil = !empty($user['foto']) ? $user['foto'] : 'default-profile.png';

// Tambah Eskul
if (isset($_POST['add_eskul'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $prestasi = mysqli_real_escape_string($conn, $_POST['prestasi']);
    mysqli_query($conn, "INSERT INTO eskul (nama, deskripsi, prestasi) VALUES ('$nama', '$deskripsi', '$prestasi')");
    header("Location: kelola_eskul.php");
    exit();
}

// Edit Eskul
if (isset($_POST['edit_eskul'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $prestasi = mysqli_real_escape_string($conn, $_POST['prestasi']);
    mysqli_query($conn, "UPDATE eskul SET nama='$nama', deskripsi='$deskripsi', prestasi='$prestasi' WHERE id=$id");
    header("Location: kelola_eskul.php");
    exit();
}

// Hapus Eskul
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM eskul WHERE id=$id");
    header("Location: kelola_eskul.php");
    exit();
}

$resultEskul = mysqli_query($conn, "SELECT * FROM eskul");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Eskul | Monitoring Eskul</title>
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

        /* Form Styling */
        .form-container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-preview {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .profile-preview img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #243b55;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center">Super-Admin</h3>
        <a href="dashboard_super-admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kelola_anggota.php"><i class="fas fa-users"></i> Kelola Users</a>
        <a href="kelola_program.php"><i class="fas fa-users"></i> Kelola Program</a>
        <a href="kelola_kegiatan.php"><i class="fas fa-calendar-alt"></i> Kelola Kegiatan</a>
        <a href="kelola_eskul.php"><i class="fas fa-calendar-alt"></i> Kelola eskul</a>
    </div>

    <div class="content">
        <nav class="navbar">
            <a class="navbar-brand" href="#">Monitoring Eskul</a>
            <div class="profile-dropdown">
                <span class="text-white"><?php echo $user['nama']; ?></span>
                <div class="dropdown">
                    <a href="#" class="text-white text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="../uploads/<?php echo $fotoProfil; ?>" alt="Foto Profil" class="profile-img">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profil</a></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <h2 class="text-center mb-4">Kelola Ekstrakurikuler</h2>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Eskul</button>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Prestasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($eskul = mysqli_fetch_assoc($resultEskul)) { ?>
                        <tr>
                            <td><?= $eskul['nama']; ?></td>
                            <td><?= $eskul['deskripsi']; ?></td>
                            <td><?= $eskul['prestasi']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $eskul['id']; ?>">Edit</button>
                                <a href="?hapus=<?= $eskul['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus eskul ini?')">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $eskul['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $eskul['id']; ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Eskul</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Nama</label>
                                                <input type="text" name="nama" class="form-control" value="<?= $eskul['nama']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control"><?= $eskul['deskripsi']; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>Prestasi</label>
                                                <textarea name="prestasi" class="form-control"><?= $eskul['prestasi']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="edit_eskul" class="btn btn-success">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah -->
        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Eskul</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Prestasi</label>
                                <textarea name="prestasi" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="add_eskul" class="btn btn-primary">Tambah</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>