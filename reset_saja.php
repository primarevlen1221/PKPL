<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php'; // Pastikan Anda sudah menghubungkan ke database

    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if ($new_password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password di database
        $query = "UPDATE users SET password = '$hashed_password' WHERE username = '$username'";
        if (mysqli_query($conn, $query)) {
            $success = "Password berhasil direset! Silakan login.";
        } else {
            $error = "Terjadi kesalahan saat mereset password. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="style/login.css">
</head>

<body>
    <div class="form">
        <h1 class="login-title">Reset Password</h1>
        <form method="POST" action="">
            <input type="text" name="username" class="login-input" placeholder="Username" required><br>
            <input type="password" name="new_password" class="login-input" placeholder="Password Baru" required><br>
            <input type="password" name="confirm_password" class="login-input" placeholder="Konfirmasi Password" required><br>
            <input type="submit" value="Reset Password" class="login-button">
        </form>
        <?php if ($error) : ?>
            <p class="error" style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success) : ?>
            <p class="success" style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>
        <p class="link">Kembali ke <a href="login.php">Login</a></p>
    </div>
</body>

</html>