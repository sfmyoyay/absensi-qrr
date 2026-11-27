<?php
session_start();

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === true) {
    header("Location: scanner.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
