<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$queryEskul = "SELECT eskul_id FROM users WHERE id = $user_id";
$resultEskul = mysqli_query($conn, $queryEskul);
$eskulData = mysqli_fetch_assoc($resultEskul);
$eskul_id = $eskulData['eskul_id'];

$queryAnggota = "SELECT * FROM users WHERE eskul_id = $eskul_id ORDER BY nama ASC";
$resultAnggota = mysqli_query($conn, $queryAnggota);

if (isset($_POST['add_anggota'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $insert = "INSERT INTO users (nama, email, password, role, eskul_id, status) 
               VALUES ('$nama', '$email', '$password', '$role', '$eskul_id', '$status')";
    if (mysqli_query($conn, $insert)) {
        header("Location: kelola_anggota.php");
        exit();
    }
}

if (isset($_POST['edit_anggota'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $update = "UPDATE users SET nama='$nama', email='$email', password='$password', role='$role', status='$status' WHERE id='$id'";
    } else {
        $update = "UPDATE users SET nama='$nama', email='$email', role='$role', status='$status' WHERE id='$id'";
    }

    if (mysqli_query($conn, $update)) {
        header("Location: kelola_anggota.php");
        exit();
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = "DELETE FROM users WHERE id='$id'";
    if (mysqli_query($conn, $delete)) {
        header("Location: kelola_anggota.php");
        exit();
    }
}

// Ambil data user untuk foto profil
$queryUser = "SELECT foto FROM users WHERE id = $user_id";
$resultUser = mysqli_query($conn, $queryUser);
$userData = mysqli_fetch_assoc($resultUser);
$foto_profile = (!empty($userData['foto'])) ? "../uploads/" . $userData['foto'] : "../uploads/profile_default.png";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota | Monitoring Eskul</title>
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

        <!-- Content -->
        <div class="content">
            <h2 class="text-center">Kelola Users</h2>

            <!-- Tombol Tambah -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Anggota
            </button>

            <!-- Modal Tambah Anggota -->
            <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Tambah Anggota</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="kelola_anggota.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-control">
                                        <option value="anggota">Anggota</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="pending">pending</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_anggota" class="btn btn-primary w-100">Tambah</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Anggota -->
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultAnggota)) { ?>
                        <tr>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['role']; ?></td>
                            <td><?= $row['status']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Anggota</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="kelola_anggota.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= $row['email']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select name="role" class="form-control">
                                                    <option value="anggota" <?= ($row['role'] == 'anggota') ? 'selected' : ''; ?>>Anggota</option>
                                                    <option value="pengurus" <?= ($row['role'] == 'pengurus') ? 'selected' : ''; ?>>Pengurus</option>
                                                    <option value="pembina" <?= ($row['role'] == 'pembina') ? 'selected' : ''; ?>>pembina</option>
                                                    <option value="super_admin" <?= ($row['role'] == 'super_admin') ? 'selected' : ''; ?>>super_admin</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="aktif" <?= ($row['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                                    <option value="nonaktif" <?= ($row['status'] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                                </select>
                                            </div>

                                            <button type="submit" name="edit_anggota" class="btn btn-warning w-100">Simpan Perubahan</button>
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