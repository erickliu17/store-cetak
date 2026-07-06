<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIM-Cetak - Transaksi Kasir</title>
    <?php if ($this->session->flashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 5px solid #dc3545; color: #721c24; background-color: #f8d7da;">
        <?= $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="box" aria-label="Close" onclick="this.parentElement.style.display='none';">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('sukses')) : ?>
    <div class="alert alert-success" role="alert">
        <?= $this->session->flashdata('sukses'); ?>
    </div>
<?php endif; ?>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        h2, h3 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #28a745; color: white; }
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background: #0056b3; }
        .nav-link { margin-bottom: 15px; display: inline-block; text-decoration: none; color: #007bff; font-weight: bold; }
        
        /* STYLE UNTUK DASHBOARD 4 KOTAK KEUANGAN */
        .dashboard-grid { display: flex; gap: 20px; margin-bottom: 25px; margin-top: 15px; }
        .card { flex: 1; padding: 20px; border-radius: 5px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card h4 { margin: 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }
        .card p { margin: 10px 0 0 0; font-size: 24px; font-weight: bold; }
        .bg-pemasukan { background-color: #28a745; border-left: 5px solid #1e7e34; }
        .bg-pengeluaran { background-color: #dc3545; border-left: 5px solid #bd2130; }
        .bg-saldo { background-color: #007bff; border-left: 5px solid #0056b3; }
    </style>
    
</head>
<body>

    <h2>Transaksi Kasir Percetakan (SIM-Cetak)</h2>
    
    <a class="nav-link" href="<?php echo base_url('index.php/produk'); ?>">← Kelola Stok Produk</a> | 
    <a class="nav-link" href="<?php echo base_url('index.php/pengeluaran'); ?>" style="color: #dc3545;">⚠ Kelola Pengeluaran Toko →</a> |
    <a class="nav-link" href="<?php echo base_url('index.php/laporan'); ?>" style="color: #28a745;">📊 Laporan Keuangan Terintegrasi →</a>  
    <li class="nav-item">
    <a href="<?= base_url('index.php/pesanan/piutang'); ?>" class="nav-link">
        <i class="nav-icon fas fa-money-check-alt"></i>
        <p>Kelola Piutang (Termin)</p>
    </a>
</li>
    <div class="dashboard-grid">
        <div class="card bg-pemasukan">
            <h4>1. Total Pendapatan (Omzet)</h4>
            <p>Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></p>
        </div>
        <div class="card" style="background-color: #fd7e14; border-left: 5px solid #d96403;">
            <h4>2. Laba Kotor (Pendapatan - Bahan)</h4>
            <p>Rp <?php echo number_format($laba_kotor, 0, ',', '.'); ?></p>
        </div>
        <div class="card bg-pengeluaran">
            <h4>3. Biaya Operasional (Listrik/Gaji)</h4>
            <p>Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></p>
        </div>
        <div class="card bg-saldo">
            <h4>4. Laba/Rugi Bersih</h4>
            <p>Rp <?php echo number_format($saldo_kas, 0, ',', '.'); ?></p>
        </div>
        
    </div>

    

    <hr>
<div class="form-box">
    <h3>Input Pesanan Baru (Kasir)</h3>
    
    <div class="form-group">
        <label>Nama Pelanggan</label>
        <input type="text" id="nama_pelanggan" required class="form-control" placeholder="Nama Pelanggan">
    </div>

    

    <hr>
    <h5>Pilih Produk & Qty</h5>

    <div class="row">
        <div class="col-md-5">
            <select id="id_produk" class="form-control">
                <option value="">-- Pilih Produk --</option>
                <?php foreach($produk as $p) : ?>
                    <option value="<?= $p['id_produk']; ?>" data-nama="<?= $p['nama_produk']; ?>" data-stok="<?= $p['stok']; ?>" data-harga="<?= $p['harga_jual']; ?>">
                        <?= $p['nama_produk']; ?> (Stok: <?= $p['stok']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" id="jumlah_beli" min="1" class="form-control" placeholder="Qty">
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-info btn-block" onclick="tambahKeKeranjang()">+ Tambah</button>
        </div>
    </div>

    <br>
    <h5>Detail Keranjang Belanja</h5>
    <table class="table table-bordered" id="tabel_keranjang">
        <thead>
            <tr class="bg-light">
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Transaksi:</th>
                <th id="grand_total">Rp 0</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

<form action="<?= base_url('index.php/pesanan/simpan_multi'); ?>" method="post" id="form_nota_utama">
    
    <input type="hidden" name="nama_pelanggan" id="hidden_nama">
    <div id="hidden_produk_inputs"></div>

    <div class="row" style="max-width: 400px; margin-top: 20px; margin-bottom: 20px;">
        <div class="col-md-12">
            
            <div class="form-group">
                <label><strong>Status Pembayaran Invoice Ini:</strong></label>
                <select name="status_bayar" id="status_bayar_langsung" class="form-control" style="border: 2px solid #007bff;" onchange="pilihTermBayar()">
                    <option value="Lunas">Lunas (Cash/Transfer)</option>
                    <option value="Belum Lunas">Belum Lunas (Termin/Piutang)</option>
                </select>
            </div>
            
            <div class="form-group" id="box_jatuh_tempo_langsung" style="display: none;">
                <label style="color: #dc3545;"><strong>Batas Tanggal Jatuh Tempo (Term):</strong></label>
                <input type="date" name="jatuh_tempo" id="jatuh_tempo_langsung" class="form-control" style="border: 2px solid #dc3545;">
            </div>

        </div>
    </div>

    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="eksekusiSimpanTransaksi()">
        💾 Simpan Transaksi & Cetak Invoice
    </button>
</form>

<script>
// Fungsi memunculkan/menyembunyikan input tanggal
function pilihTermBayar() {
    const status = document.getElementById('status_bayar_langsung').value;
    const boxTerm = document.getElementById('box_jatuh_tempo_langsung');
    if (status === 'Belum Lunas') {
        boxTerm.style.display = 'block';
        // Auto-set tanggal hari ini sebagai default jika belum lunas
        document.getElementById('jatuh_tempo_langsung').value = new Date().toISOString().slice(0, 10);
    } else {
        boxTerm.style.display = 'none';
        document.getElementById('jatuh_tempo_langsung').value = '';
    }
}

// Fungsi pengaman validasi sebelum submit form
function eksekusiSimpanTransaksi() {
    if (typeof keranjang === 'undefined' || keranjang.length === 0) {
        alert('Keranjang belanja masih kosong!');
        return;
    }
    
    const namaInput = document.getElementById('nama_pelanggan');
    if (!namaInput || !namaInput.value.trim()) {
        alert('Nama pelanggan wajib diisi!');
        return;
    }

    const statusBayar = document.getElementById('status_bayar_langsung').value;
    const tanggalInput = document.getElementById('jatuh_tempo_langsung');

    if (statusBayar === 'Belum Lunas' && (!tanggalInput || !tanggalInput.value)) {
        alert('Untuk status Belum Lunas, Batas Tanggal Jatuh Tempo (Term) Wajib Diisi!');
        return;
    }

    // Isi nama pelanggan ke input hidden form
    document.getElementById('hidden_nama').value = namaInput.value;

    // Masukkan daftar produk dari keranjang belanja
    const areaHiddenInput = document.getElementById('hidden_produk_inputs');
    areaHiddenInput.innerHTML = ''; 
    keranjang.forEach((item, indeks) => {
        areaHiddenInput.innerHTML += `
            <input type="hidden" name="produk_keranjang[${indeks}][id_produk]" value="${item.id_produk}">
            <input type="hidden" name="produk_keranjang[${indeks}][jumlah_beli]" value="${item.jumlah_beli}">
        `;
    });

    // KIRIM LANGSUNG FORM UTAMA
    document.getElementById('form_nota_utama').submit();
}
</script>   
        
        <hr style="border-top: 3px double #ccc; margin-top: 40px; margin-bottom: 30px;">

    <div class="row">
        <div class="col-md-12">
            <h4 style="margin-bottom: 15px; color: #333;">📜 Daftar Riwayat Transaksi (Pesanan)</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">No. Invoice</th>
                            <th width="25%">Nama Pelanggan</th>
                            <th width="20%">Tanggal Pesan</th>
                            <th width="15%">Total Harga</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php 
    $no = 1; 
    if (!empty($pesanan)) {
        foreach($pesanan as $p) : 
    ?>
    <tr>
        <td class="text-center"><?= $no++; ?></td>
        <td>
    <strong>
        <?php 
        $no_urut = str_pad($p['id_pesanan'], 4, '0', STR_PAD_LEFT);
        $bln = date('m', strtotime($p['tgl_pesan']));
        $thn = date('Y', strtotime($p['tgl_pesan']));
        echo $no_urut . '/CR/SIM/' . $bln . '/' . $thn;
        ?>
    </strong>
</td>
        <td><?= $p['nama_pelanggan']; ?></td>
        <td><?= date('d-m-Y H:i', strtotime($p['tgl_pesan'])); ?></td>
        <td>Rp <?= number_format($p['total_harga'], 0, ',', '.'); ?></td>
        <td class="text-center" style="vertical-align: middle;">
    <?php if($p['status_bayar'] == 'Lunas'): ?>
        <span class="badge badge-success" style="padding: 6px 12px; font-size: 11px; border-radius: 4px; display: block; margin-bottom: 2px;">✓ LUNAS</span>
    <?php else: ?>
        <span class="badge badge-danger" style="padding: 6px 12px; font-size: 11px; border-radius: 4px; display: block; margin-bottom: 5px;">✗ BELUM LUNAS</span>
        <a href="<?= base_url('index.php/pesanan/set_lunas/'.$p['id_pesanan']); ?>" class="btn btn-xs btn-success btn-block" style="font-size: 10px; padding: 2px 5px;" onclick="return confirm('Set lunas invoice ini?')">
            Pelunasan
        </a>
    <?php endif; ?>
</td>

<td class="text-center" style="vertical-align: middle;">
    <?php 
    $status_p = $p['status_produksi'];
    $badge_class = 'badge-secondary';
    if($status_p == 'Proses') $badge_class = 'badge-warning';
    if($status_p == 'Selesai') $badge_class = 'badge-success';
    ?>
    <span class="badge <?= $badge_class; ?>" style="padding: 6px 12px; font-size: 11px; border-radius: 4px; display: block; margin-bottom: 5px;"><?= strtoupper($status_p); ?></span>
    
    <form action="<?= base_url('index.php/pesanan/update_status_produksi/'.$p['id_pesanan']); ?>" method="post" style="margin: 0;">
        <select name="status_produksi" onchange="this.form.submit()" class="form-control form-control-sm" style="font-size: 11px; height: 26px; padding: 2px 5px;">
            <option value="">-- Ubah Status --</option>
            <option value="Antri">Antri</option>
            <option value="Proses">Proses</option>
            <option value="Selesai">Selesai</option>
        </select>
    </form>
</td>

<td class="text-center" style="vertical-align: middle;">
    <div class="btn-group-vertical btn-group-sm" style="width: 100%;">
        <a href="<?= base_url('index.php/pesanan/cetak_invoice/'.$p['id_pesanan']); ?>" target="_blank" class="btn btn-warning" style="font-weight: bold; margin-bottom: 2px; border-radius: 4px !important;">
            🖨️ Cetak Struk
        </a>
        <a href="<?= base_url('index.php/pesanan/hapus_pesanan/'.$p['id_pesanan']); ?>" class="btn btn-danger" style="border-radius: 4px !important;" onclick="return confirm('Hapus seluruh data invoice ini?')">
            🗑️ Hapus
        </a>
    </div>
</td>
    </tr>
    <?php 
        endforeach; 
    } else { 
    ?>
    <tr>
        <td colspan="8" class="text-center text-muted">Belum ada riwayat transaksi.</td>
    </tr>
    <?php } ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
let keranjang = [];

function tambahKeKeranjang() {
    const selectProduk = document.getElementById('id_produk');
    const optionTerpilih = selectProduk.options[selectProduk.selectedIndex];
    
    if (!selectProduk.value) return alert('Pilih produk terlebih dahulu!');
    
    const id_produk = selectProduk.value;
    const nama_produk = optionTerpilih.getAttribute('data-nama');
    const harga_jual = parseInt(optionTerpilih.getAttribute('data-harga'));
    const stok = parseInt(optionTerpilih.getAttribute('data-stok'));
    const jumlah_beli = parseInt(document.getElementById('jumlah_beli').value);

    if (!jumlah_beli || jumlah_beli <= 0) return alert('Masukkan Qty yang valid!');
    if (jumlah_beli > stok) return alert('Stok tidak mencukupi!');

    // Cek jika produk sudah ada di keranjang, tinggal update qty
    const indeksAda = keranjang.findIndex(item => item.id_produk === id_produk);
    if (indeksAda > -1) {
        if ((keranjang[indeksAda].jumlah_beli + jumlah_beli) > stok) return alert('Total Qty melebihi stok!');
        keranjang[indeksAda].jumlah_beli += jumlah_beli;
        keranjang[indeksAda].subtotal = keranjang[indeksAda].jumlah_beli * harga_jual;
    } else {
        // Tambah item baru ke array keranjang
        keranjang.push({
            id_produk, nama_produk, harga_jual, jumlah_beli,
            subtotal: harga_jual * jumlah_beli
        });
    }

    renderKeranjang();
    document.getElementById('jumlah_beli').value = '';
}

function hapusItem(indeks) {
    keranjang.splice(indeks, 1);
    renderKeranjang();
}

function renderKeranjang() {
    const tbody = document.querySelector('#tabel_keranjang tbody');
    tbody.innerHTML = '';
    let total = 0;

    keranjang.forEach((item, indeks) => {
        total += item.subtotal;
        tbody.innerHTML += `
            <tr>
                <td>${item.nama_produk}</td>
                <td>Rp ${item.harga_jual.toLocaleString()}</td>
                <td>${item.jumlah_beli}</td>
                <td>Rp ${item.subtotal.toLocaleString()}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusItem(${indeks})">Hapus</button></td>
            </tr>
        `;
    });

    document.getElementById('grand_total').innerText = 'Rp ' + total.toLocaleString();
}

function submitTransaksiUtama() {
    // 1. Cek apakah keranjang ada isinya
    if (typeof keranjang === 'undefined' || keranjang.length === 0) {
        alert('Keranjang belanja masih kosong!');
        return;
    }
    
    // 2. Ambil nilai Nama Pelanggan secara aman
    const namaPelanggan = document.getElementById('nama_pelanggan') ? document.getElementById('nama_pelanggan').value.trim() : '';
    if (!namaPelanggan) {
        alert('Nama pelanggan wajib diisi!');
        return;
    }

    // 3. Ambil nilai Status dan Tanggal secara aman tanpa takut crash
    const statusVisual = document.getElementById('status_bayar_visual') ? document.getElementById('status_bayar_visual').value : 'Lunas';
    const tanggalVisual = document.getElementById('jatuh_tempo_visual') ? document.getElementById('jatuh_tempo_visual').value : '';

    // 4. Validasi jika Belum Lunas tapi tanggal term kosong
    if (statusVisual === 'Belum Lunas' && !tanggalVisual) {
        alert('Untuk status Belum Lunas, Batas Tanggal Jatuh Tempo (Term) Wajib Diisi!');
        return;
    }

    // 5. ISI DATA KE INPUT HIDDEN UTAMA
    if (document.getElementById('hidden_nama')) document.getElementById('hidden_nama').value = namaPelanggan;
    if (document.getElementById('hidden_status')) document.getElementById('hidden_status').value = statusVisual;
    if (document.getElementById('hidden_term')) document.getElementById('hidden_term').value = (statusVisual === 'Belum Lunas') ? tanggalVisual : '';

    // 6. Generasi input hidden untuk list produk di keranjang
    const areaHiddenInput = document.getElementById('hidden_produk_inputs');
    if (areaHiddenInput) {
        areaHiddenInput.innerHTML = ''; 
        keranjang.forEach((item, indeks) => {
            areaHiddenInput.innerHTML += `
                <input type="hidden" name="produk_keranjang[${indeks}][id_produk]" value="${item.id_produk}">
                <input type="hidden" name="produk_keranjang[${indeks}][jumlah_beli]" value="${item.jumlah_beli}">
            `;
        });
    }

    // 7. Eksekusi Submit Form Utama
    const formUtama = document.getElementById('form_nota_utama');
    if (formUtama) {
        formUtama.submit();
    } else {
        alert('Error: Form utama dengan id="form_nota_utama" tidak ditemukan!');
    }
}
function cekStatusBayar() {
    const status = document.getElementById('status_bayar_visual').value;
    const boxTerm = document.getElementById('box_jatuh_tempo');
    if (status === 'Belum Lunas') {
        boxTerm.style.display = 'block';
        // Mengisi otomatis input jatuh_tempo_visual dengan tanggal hari ini
        document.getElementById('jatuh_tempo_visual').value = new Date().toISOString().slice(0, 10);
    } else {
        boxTerm.style.display = 'none';
        document.getElementById('jatuh_tempo_visual').value = '';
    }
}

</script>
</body>
</html>