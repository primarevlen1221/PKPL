<?php
$host = "localhost"; // atau IP server database
$user = "root"; // username database
$password = ""; // password database
$dbname = "login_system"; // nama database

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
