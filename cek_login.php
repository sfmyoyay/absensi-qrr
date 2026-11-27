<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Header anti-cache agar tombol Back browser tidak menampilkan halaman setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: login.php");
    exit;
}
?>
