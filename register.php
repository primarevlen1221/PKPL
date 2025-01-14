<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php'; // Pastikan Anda sudah menghubungkan ke database

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($username)) {
        $error = "Username tidak boleh kosong!";
    } elseif (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        $error = "Username hanya boleh mengandung huruf dan angka!";
    } elseif (strlen($password) < 8) {
        $error = "Password harus terdiri dari minimal 8 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Cek apakah username sudah ada di database
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan pengguna baru ke database
            $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" type="text/css" href="style/login.css">
</head>

<body>
    <div class="form">
        <h1 class="login-title">Registrasi</h1>
        <form method="POST" action="">
            <input type="text" name="username" class="login-input" placeholder="Username" required><br>
            <input type="password" name="password" class="login-input" placeholder="Password" required><br>
            <input type="password" name="confirm_password" class="login-input" placeholder="Konfirmasi Password" required><br>
            <input type="submit" value="Daftar" class="login-button">
        </form>
        <?php if ($error) : ?>
            <p class="error" style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success) : ?>
            <p class="success" style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>
        <p class="link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>

</html>