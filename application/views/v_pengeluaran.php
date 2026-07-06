<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIM-Cetak - Pengeluaran Toko</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        h2, h3 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #dc3545; color: white; }
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #dc3545; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background: #bd2130; }
        .nav-link { margin-bottom: 15px; display: inline-block; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Manajemen Pengeluaran Operasional</h2>
    <a class="nav-link" href="<?php echo base_url('index.php/pesanan'); ?>">← Kembali ke Kasir</a>
    <hr>

    <div class="form-box">
        <h3>Input Pengeluaran Baru</h3>
        <form action="<?php echo base_url('index.php/pengeluaran/simpan'); ?>" method="post">
            <div class="form-group">
                <label>Kategori Pengeluaran</label>
                <select name="kategori" required>
                    <option value="Bahan Baku (Tinta/Kertas)">Bahan Baku (Tinta/Kertas)</option>
                    <option value="Operasional Mesin & Listrik">Operasional Mesin & Listrik</option>
                    <option value="Maintenance / Servis Mesin">Maintenance / Servis Mesin</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nominal (Rp)</label>
                <input type="number" name="nominal" required placeholder="Contoh: 150000">
            </div>
            <div class="form-group">
                <label>Keterangan / Catatan</label>
                <textarea name="keterangan" rows="3" required placeholder="Contoh: Beli tinta Epson Ori warna Hitam 2 botol"></textarea>
            </div>
            <button type="submit">Simpan Pengeluaran</button>
        </form>
    </div>

    <h3>Log Pengeluaran Toko</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Keterangan / Catatan</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($pengeluaran as $p) : ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $p['tgl_pengeluaran']; ?></td>
                <td><span style="background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;"><?php echo $p['kategori']; ?></span></td>
                <td><?php echo $p['keterangan']; ?></td>
                <td style="color: #dc3545; font-weight: bold;">Rp <?php echo number_format($p['nominal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pengeluaran)) : ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #888;">Belum ada data pengeluaran.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>