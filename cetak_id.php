<?php
require_once 'auth/cek_login.php';
require_once 'config/koneksi.php';

$query = mysqli_query($koneksi, "SELECT id_pegawai, nip, nama, qr_code FROM pegawai ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak ID Card QR - PDAM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>

    <nav class="navbar no-print">
        <div class="navbar-brand">Absensi QR PDAM</div>
        <div class="navbar-menu">
            <a href="scanner.php">Scanner</a>
            <a href="rekap.php">Rekap Absensi</a>
            <a href="cetak_id.php" class="active">Cetak ID Card</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container-wide">
            <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2>Daftar ID Card Pegawai</h2>
                    <p class="subtitle">Cetak kartu ini atau scan langsung QR Code-nya ke kamera absensi.</p>
                </div>
                <button onclick="window.print()" class="btn">Cetak Semua ID Card</button>
            </div>

            <div class="card-grid">
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <div class="id-card">
                            <div class="instansi">Kartu Absensi PDAM</div>
                            <h3><?= htmlspecialchars($row['nama']); ?></h3>
                            <p class="nip">NIP: <?= htmlspecialchars($row['nip']); ?></p>
                            <div class="qr-box" id="qr-<?= (int)$row['id_pegawai']; ?>" data-qr="<?= htmlspecialchars($row['qr_code']); ?>"></div>
                            <p class="kode-qr"><?= htmlspecialchars($row['qr_code']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #64748b;">Belum ada data pegawai di database.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.qr-box').forEach(function(el) {
            const kode = el.getAttribute('data-qr');
            new QRCode(el, {
                text: kode,
                width: 140,
                height: 140
            });
        });
    </script>

</body>
</html>
