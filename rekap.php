<?php
require_once 'auth/cek_login.php';
require_once 'config/koneksi.php';

$tanggal_pilihan = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$stmt = mysqli_prepare($koneksi, "
    SELECT pegawai.nip, pegawai.nama, absensi.tanggal, absensi.jam_masuk, absensi.status
    FROM absensi
    JOIN pegawai ON absensi.id_pegawai = pegawai.id_pegawai
    WHERE absensi.tanggal = ?
    ORDER BY absensi.jam_masuk ASC
");
mysqli_stmt_bind_param($stmt, "s", $tanggal_pilihan);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - PDAM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">Absensi QR PDAM</div>
        <div class="navbar-menu">
            <a href="scanner.php">Scanner</a>
            <a href="rekap.php" class="active">Rekap Absensi</a>
            <a href="cetak_id.php">Cetak ID Card</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container-wide">
            <h2>Rekap Absensi Pegawai</h2>
            <p class="subtitle">Menampilkan data kehadiran tanggal: <b><?= htmlspecialchars($tanggal_pilihan); ?></b></p>

            <form method="GET" action="" class="filter-box">
                <label for="tanggal"><strong>Pilih Tanggal:</strong></label>
                <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_pilihan); ?>">
                <button type="submit" class="btn">Tampilkan</button>
                <a href="rekap.php" class="btn btn-secondary">Hari Ini</a>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nip']); ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                <td><?= htmlspecialchars($row['jam_masuk'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                        $status = strtolower($row['status']);
                                        $badge_class = 'badge-hadir';
                                        if ($status === 'terlambat') $badge_class = 'badge-terlambat';
                                        elseif ($status === 'izin') $badge_class = 'badge-izin';
                                        elseif ($status === 'sakit') $badge_class = 'badge-sakit';
                                    ?>
                                    <span class="badge <?= $badge_class; ?>">
                                        <?= htmlspecialchars(ucfirst($row['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #64748b;">
                                Belum ada pegawai yang absen pada tanggal ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
