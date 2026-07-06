<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Percetakan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        .no-print { background: #fff; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 3px; text-decoration: none; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #aaa; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .rekap-box { margin-top: 20px; font-size: 16px; line-height: 1.6; max-width: 500px; }
        .text-right { text-align: right; font-weight: bold; }
        
        /* CSS KHUSUS SAAT DICETAK/DIPRINT */
        @media print {
            body { background: #fff; margin: 10px; }
            .no-print { display: none !important; }
            table { border: 1px solid #000; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <h2>Filter Laporan Keuangan</h2>
        <a href="<?php echo base_url('index.php/pesanan'); ?>" style="color: #007bff; text-decoration: none; font-weight: bold;">← Kembali ke Kasir</a>
        <hr>
        <form action="<?php echo base_url('index.php/laporan'); ?>" method="get" style="display: flex; gap: 15px; align-items: center;">
            <div>
                <label>Tanggal Mulai: </label>
                <input type="date" name="tgl_awal" value="<?php echo $tgl_awal; ?>" required>
            </div>
            <div>
                <label>Tanggal Selesai: </label>
                <input type="date" name="tgl_akhir" value="<?php echo $tgl_akhir; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Filter Data</button>
            <button type="button" onclick="window.print()" class="btn btn-success">🖨️ Cetak / Print PDF</button>
        </form>
    </div>

    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="margin: 0; text-transform: uppercase;">Laporan Realisasi Keuangan & Laba Rugi</h2>
        <h3 style="margin: 5px 0 0 0; color: #555;">Sistem Informasi Manajemen Percetakan (SIM-Cetak)</h3>
        <p style="margin: 5px 0 0 0; font-style: italic;">Periode: <strong><?php echo date('d-m-Y', strtotime($tgl_awal)); ?></strong> s/d <strong><?php echo date('d-m-Y', strtotime($tgl_akhir)); ?></strong></p>
    </div>

    <h3>Ringkasan Laba Rugi</h3>
    <table style="max-width: 600px;">
        <tr>
            <td>1. Total Pendapatan Cetak (Omzet)</td>
            <td class="text-right" style="color: #28a745;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>2. Nilai Bahan Baku Terpakai (HPP)</td>
            <td class="text-right" style="color: #fd7e14;">(Rp <?php echo number_format($bahan_terpakai, 0, ',', '.'); ?>)</td>
        </tr>
        <tr style="background: #f9f9f9; font-weight: bold;">
            <td>LABA KOTOR (Pendapatan - Bahan)</td>
            <td class="text-right">Rp <?php echo number_format($laba_kotor, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>3. Total Biaya Operasional (Listrik, Gaji, dll)</td>
            <td class="text-right" style="color: #dc3545;">(Rp <?php echo number_format($operasional, 0, ',', '.'); ?>)</td>
        </tr>
        <tr style="background: #e2e3e5; font-weight: bold; font-size: 18px;">
            <td>LABA / RUGI BERSIH</td>
            <td class="text-right" style="color: <?php echo $laba_bersih >= 0 ? '#28a745' : '#dc3545'; ?>;">
                Rp <?php echo number_format($laba_bersih, 0, ',', '.'); ?>
            </td>
        </tr>
    </table>

    <h3 style="margin-top: 30px;">Detail Transaksi Penjualan Lunas</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Total Omzet Transaksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($pesanan_terfilter as $pt) : ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $pt['tgl_pesan']; ?></td>
                <td><?php echo $pt['nama_pelanggan']; ?></td>
                <td>Rp <?php echo number_format($pt['total_harga'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pesanan_terfilter)) : ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #888;">Tidak ada transaksi kasir pada periode tanggal ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>