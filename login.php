<?php
session_start();
include 'config.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hashing MD5

    // Query untuk mencari user berdasarkan email dan password
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect berdasarkan role
        if ($user['role'] == 'anggota') {
            header("Location: anggota/dashboard_anggota.php");
        } elseif ($user['role'] == 'pengurus') {
            header("Location: pengurus/dashboard_pengurus.php");
        } elseif ($user['role'] == 'pembina') {
            header("Location: pembina/dashboard_pembina.php");
        }  elseif ($user['role'] == 'super_admin') {
            header("Location: admin/dashboard_super-admin.php");
        }
        exit();
    } else {
        echo "<script>alert('Email atau password salah!'); window.location='login.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Monitoring Eskul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            text-align: center;
            color: white;
            backdrop-filter: blur(10px);
        }
        .form-control {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-login {
            background: #ff8c00;
            border: none;
            font-weight: bold;
        }
        .btn-login:hover {
            background: #ff6a00;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Monitoring Eskul</h2>
        <p>Silakan login untuk mengakses sistem</p>
        <form action="" method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login btn-block w-100">Login</button>
        </form>
    </div>

</body>
</html>
