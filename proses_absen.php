<?php
require_once 'auth/cek_login.php';
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['qr_code'])) {
    echo "<div class='alert error'>Permintaan tidak valid.</div>";
    exit;
}

$qr_code = trim($_POST['qr_code']);
$tanggal = date('Y-m-d');
$jam     = date('H:i:s');

// 1. Cek apakah QR Code terdaftar pada data pegawai
$stmt_pegawai = mysqli_prepare($koneksi, "SELECT id_pegawai, nip, nama FROM pegawai WHERE qr_code = ? LIMIT 1");
mysqli_stmt_bind_param($stmt_pegawai, "s", $qr_code);
mysqli_stmt_execute($stmt_pegawai);
$res_pegawai = mysqli_stmt_get_result($stmt_pegawai);

if ($pegawai = mysqli_fetch_assoc($res_pegawai)) {
    $id_pegawai = $pegawai['id_pegawai'];
    $nama = htmlspecialchars($pegawai['nama']);
    $nip  = htmlspecialchars($pegawai['nip']);

    // 2. Cek apakah pegawai sudah absen hari ini
    $stmt_cek = mysqli_prepare($koneksi, "SELECT id, jam_masuk FROM absensi WHERE id_pegawai = ? AND tanggal = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_cek, "is", $id_pegawai, $tanggal);
    mysqli_stmt_execute($stmt_cek);
    $res_cek = mysqli_stmt_get_result($stmt_cek);

    if ($sudah = mysqli_fetch_assoc($res_cek)) {
        $jam_tercatat = htmlspecialchars($sudah['jam_masuk']);
        echo "<div class='alert warning'>Halo, <b>$nama</b> ($nip).<br>Anda sudah absen hari ini pada pukul <b>$jam_tercatat</b>.</div>";
    } else {
        // 3. Tentukan status (contoh: lewat pukul 08:00:00 dianggap Terlambat)
        $status = ($jam > '08:00:00') ? 'Terlambat' : 'Hadir';

        $stmt_simpan = mysqli_prepare($koneksi, "INSERT INTO absensi (id_pegawai, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_simpan, "isss", $id_pegawai, $tanggal, $jam, $status);

        if (mysqli_stmt_execute($stmt_simpan)) {
            echo "<div class='alert success'>Absensi Berhasil!<br><b>$nama</b> ($nip)<br>Jam: $jam &bull; Status: <b>$status</b></div>";
        } else {
            echo "<div class='alert error'>Gagal menyimpan data absensi ke database.</div>";
        }
    }
} else {
    echo "<div class='alert error'>QR Code tidak dikenali atau belum terdaftar!</div>";
}
?>
