<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIM-Cetak - Manajemen Produk</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        h2, h3 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background: #218838; }
    </style>
</head>
<body>

    <h2>Aplikasi Toko Percetakan (SIM-Cetak)</h2>
    <hr>
    <a class="nav-link" href="<?php echo base_url('index.php/pesanan'); ?>" style="background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; display: inline-block; margin-bottom: 15px;">
        Go to Transaksi Kasir →
    </a>
    <div class="form-box">
    <h3>Tambah Produk Baru</h3>
    <form action="<?php echo base_url('index.php/produk/simpan'); ?>" method="post">
        <div class="form-group">
            <label>Nama Produk / Bahan Baku</label>
            <input type="text" name="nama_produk" required placeholder="Contoh: Cetak Kwitansi">
        </div>
        <div class="form-group">
            <label>Stok Awal</label>
            <input type="number" name="stok" required min="0" placeholder="0">
        </div>
        <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" required placeholder="Contoh: pcs, lembar">
        </div>
        
        <div class="form-group">
            <label>Harga Beli (Modal)</label>
            <input type="number" name="harga_beli" required min="0" placeholder="Rp Harga Modal">
        </div>
        <div class="form-group">
            <label>Harga Jual (Kasir)</label>
            <input type="number" name="harga_jual" required min="0" placeholder="Rp Harga Jual">
        </div>
        
        <button type="submit">Simpan Produk</button>
    </form>
</div>
    <div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 25px; border-radius: 5px; border-top: 5px solid #fd7e14;">
        <h3 style="margin-top:0; color: #fd7e14;">➕ Form Pembelian Stok Bahan Baku (Restock)</h3>
        <p style="font-size: 13px; color: #666; margin-top: -5px;">Gunakan form ini saat membeli kertas, tinta, atau banner. Sistem akan otomatis menambah stok produk tanpa merusak laporan operasional.</p>
        
        <?php if($this->session->flashdata('sukses')): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 3px;">
                <?php echo $this->session->flashdata('sukses'); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo base_url('index.php/produk/proses_beli'); ?>" method="post" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
    <div style="flex: 2; min-width: 200px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Pilih Bahan Baku</label>
        <select name="id_produk" required style="width:100%; padding: 8px;">
            <option value="">-- Pilih Bahan --</option>
            <?php foreach($produk as $p) : ?>
                <option value="<?php echo $p['id_produk']; ?>"><?php echo $p['nama_produk']; ?> (Stok Sekarang: <?php echo $p['stok']; ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="width: 120px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Jumlah (Qty)</label>
        <input type="number" name="jumlah_beli" required min="1" placeholder="Misal: 10" style="width:100%; padding: 8px;">
    </div>
    <div style="flex: 1; min-width: 150px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Harga Beli Satuan (Modal)</label>
        <input type="number" name="harga_beli_satuan" required min="1" placeholder="Rp" style="width:100%; padding: 8px;">
    </div>
    
    <div style="flex: 1; min-width: 150px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px; color: #28a745;">Harga Jual Baru</label>
        <input type="number" name="harga_jual_baru" required min="1" placeholder="Rp Jual Kasir" style="width:100%; padding: 8px; border: 1px solid #28a745; border-radius: 3px;">
    </div>

    <div>
        <button type="submit" style="background: #fd7e14; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 3px; font-weight: bold;">
            🛒 Simpan Pembelian Stok
        </button>
    </div>
</form>
    </div>
    <h3>Data Stok Bahan & Produk</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Stok Gudang</th>
            <th>Harga Beli (Modal)</th>
            <th>Harga Jual (Kasir)</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($produk as $p) : ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $p['nama_produk']; ?></td>
            <td><strong><?php echo $p['stok']; ?> <?php echo $p['satuan']; ?></strong></td>
            <td style="color: #dc3545;">Rp <?php echo number_format($p['harga_beli'], 0, ',', '.'); ?></td>
            <td style="color: #28a745;">Rp <?php echo number_format($p['harga_jual'], 0, ',', '.'); ?></td>
            <td>
    <button type="button" class="btn btn-sm btn-primary" onclick="bukaPopupEdit('<?php echo $p['id_produk']; ?>')">
        Edit
    </button>
    
    <a href="<?php echo base_url('index.php/produk/hapus/'.$p['id_produk']); ?>" 
       class="btn btn-sm btn-danger" 
       onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?php echo $p['nama_produk']; ?>? Semua data stok produk ini akan hilang.');" 
       style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-left: 5px;">
        Hapus
    </a>
</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php foreach($produk as $p) : ?>
<div id="popupEdit_<?php echo $p['id_produk']; ?>" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    
    <div style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">
            <h3 style="margin: 0;">Edit Data: <?php echo $p['nama_produk']; ?></h3>
            <button type="button" onclick="tutupPopupEdit('<?php echo $p['id_produk']; ?>')" style="background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; font-weight: bold;">X</button>
        </div>

        <form action="<?php echo base_url('index.php/produk/update'); ?>" method="post">
            <input type="hidden" name="id_produk" value="<?php echo $p['id_produk']; ?>">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Nama Produk / Bahan Baku</label>
                <input type="text" name="nama_produk" value="<?php echo $p['nama_produk']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Stok Gudang</label>
                <input type="number" name="stok" value="<?php echo $p['stok']; ?>" required min="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Satuan</label>
                <input type="text" name="satuan" value="<?php echo $p['satuan']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Harga Beli (Harga Modal)</label>
                <input type="number" name="harga_beli" value="<?php echo $p['harga_beli']; ?>" required min="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Harga Jual (Kasir)</label>
                <input type="number" name="harga_jual" value="<?php echo $p['harga_jual']; ?>" required min="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="text-align: right; border-top: 1px solid #ddd; padding-top: 15px;">
                <button type="button" onclick="tutupPopupEdit('<?php echo $p['id_produk']; ?>')" style="background: #6c757d; color: white; border: none; padding: 8px 16px; margin-right: 10px; cursor: pointer; border-radius: 4px;">Batal</button>
                <button type="submit" style="background: #28a745; color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 4px;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>
<script>
function bukaPopupEdit(id) {
    document.getElementById('popupEdit_' + id).style.display = 'block';
}

function tutupPopupEdit(id) {
    document.getElementById('popupEdit_' + id).style.display = 'none';
}

// Menutup pop-up jika pengguna mengklik area luar kotak form
window.onclick = function(event) {
    <?php foreach($produk as $p) : ?>
    var modal = document.getElementById('popupEdit_<?php echo $p['id_produk']; ?>');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
    <?php endforeach; ?>
}
</script>
</body>
</html>