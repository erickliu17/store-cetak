<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Piutang - STORE-CETAK</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 4px solid #dc3545; }
        .table thead th { background-color: #dc3545; color: white; vertical-align: middle; text-align: center; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="mb-3">
        <a href="<?= base_url('index.php/pesanan'); ?>" class="btn btn-secondary font-weight-bold">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu Kasir
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h3 class="card-title m-0 font-weight-bold text-danger"><i class="fas fa-money-check-alt"></i> Ringkasan & Pelunasan Piutang Pelanggan</h3>
        </div>
        <div class="card-body">

            <?php if ($this->session->flashdata('sukses')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('sukses'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="this.parentElement.style.display='none';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Invoice / ID</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal Order</th>
                            <th>Jatuh Tempo (Term)</th>
                            <th>Total Tagihan</th>
                            <th>Status Pembatasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($piutang)) {
                            echo '<tr><td colspan="8" class="text-center text-muted p-4">🟢 Transaksi aman, tidak ada piutang outstanding saat ini.</td></tr>';
                        } else {
                            $no = 1;
                            foreach ($piutang as $row) {
                                // --- PROSES BACKUP JIKA KOLOM TANGGAL BERBEDA NAMA ---
                                $tgl_order_mentah = isset($row['tgl_pesan']) ? $row['tgl_pesan'] : (isset($row['tanggal']) ? $row['tanggal'] : date('Y-m-d H:i:s'));
                                $tgl_jt_mentah    = isset($row['jatuh_tempo']) ? $row['jatuh_tempo'] : date('Y-m-d');

                                // Format nomor invoice buatan
                                $format_invoice = 'INV-' . str_pad($row['id_pesanan'], 4, '0', STR_PAD_LEFT);

                                // Cek status overdue secara aman
                                $sudah_lewat = (strtotime($tgl_jt_mentah) < strtotime(date('Y-m-d')));
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center font-weight-bold text-primary"><?= $format_invoice; ?></td>
                                    <td class="font-weight-bold"><?= isset($row['nama_pelanggan']) ? $row['nama_pelanggan'] : '-'; ?></td>
                                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($tgl_order_mentah)); ?></td>
                                    <td class="text-center font-weight-bold text-danger"><?= date('d-m-Y', strtotime($tgl_jt_mentah)); ?></td>
                                    <td class="text-right font-weight-bold text-dark">Rp <?= isset($row['total_harga']) ? number_format($row['total_harga'], 0, ',', '.') : '0'; ?></td>
                                    <td class="text-center">
                                        <?php if ($sudah_lewat) : ?>
                                            <span class="badge badge-danger px-3 py-2">🔴 BLOKIR KASIR (Overdue)</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning px-3 py-2 text-dark">🟡 Piutang Berjalan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('index.php/pesanan/lunaskan_nota/' . $row['id_pesanan']); ?>" 
                                           class="btn btn-success btn-sm font-weight-bold" 
                                           onclick="return confirm('Apakah Anda yakin pelanggan ini sudah membayar lunas tagihan?')">
                                            <i class="fas fa-check-circle"></i> Lunaskan
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>