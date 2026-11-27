<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === true) {
    header("Location: scanner.php");
    exit;
}

$error = '';
$info = isset($_GET['pesan']) && $_GET['pesan'] === 'logout' ? 'Anda telah berhasil logout.' : '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $valid = password_verify($password, $user['password']) || (md5($password) === $user['password']);

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['status_login'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: scanner.php");
            exit;
        }
    }

    $error = "Username atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Absensi QR PDAM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-box">
            <h2>Login Petugas PDAM</h2>
            <p class="subtitle">Silakan masuk terlebih dahulu untuk membuka aplikasi absensi</p>

            <?php if (!empty($info)): ?>
                <div class="alert success" style="margin-bottom: 12px;">
                    <?= htmlspecialchars($info); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert error" style="margin-bottom: 12px;">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" required autofocus>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="btn btn-full">Masuk</button>
            </form>
        </div>
    </div>

</body>
</html>
