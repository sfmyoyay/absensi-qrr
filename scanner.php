<?php
require_once 'auth/cek_login.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Absensi QR - PDAM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">Absensi QR PDAM</div>
        <div class="navbar-menu">
            <a href="scanner.php" class="active">Scanner</a>
            <a href="rekap.php">Rekap Absensi</a>
            <a href="cetak_id.php">Cetak ID Card</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container-scanner">
            <h2>Scan QR Code Pegawai</h2>
            <p class="subtitle">Arahkan QR Code pada ID Card ke kamera</p>

            <div id="reader"></div>
            <div id="result-container"></div>
        </div>
    </div>

    <script>
        let sedangMemproses = false;

        function onScanSuccess(decodedText) {
            if (sedangMemproses) return;
            sedangMemproses = true;

            html5QrcodeScanner.pause(true);

            fetch('proses_absen.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_code=' + encodeURIComponent(decodedText)
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('result-container').innerHTML = html;

                setTimeout(() => {
                    document.getElementById('result-container').innerHTML = '';
                    sedangMemproses = false;
                    html5QrcodeScanner.resume();
                }, 2500);
            })
            .catch(() => {
                document.getElementById('result-container').innerHTML =
                    "<div class='alert error'>Gagal terhubung ke server.</div>";
                sedangMemproses = false;
                html5QrcodeScanner.resume();
            });
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</body>
</html>
