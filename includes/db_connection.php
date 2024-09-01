<?php
// db_connect.php

$servername = "192.168.1.11:3306";
$username = "root";
$password = "root";
$dbname = "ucp"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
