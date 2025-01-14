<?php
session_start();
session_destroy();

// Hapus cookie jika ada
if (isset($_COOKIE['auth_token'])) {
    unset($_COOKIE['auth_token']);
    setcookie('auth_token', '', time() - 3600, '/'); // Hapus cookie
}

// Redirect ke halaman utama
header("Location: index.php");
exit();
?>
