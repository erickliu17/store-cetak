<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice SIM-Cetak</title>    
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 14px; 
            color: #000; 
            line-height: 1.4;
            padding: 10px;
        }
        .invoice-container { 
            max-width: 550px; 
            margin: 0 auto; 
            border: 1px dashed #000; 
            padding: 20px; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .header h2 { margin: 0 0 5px 0; font-size: 20px; }
        .header p { margin: 0; font-size: 12px; }
        .dashed-line { 
            border-top: 1px dashed #000; 
            margin: 10px 0; 
        }
        .table-info, .table-items { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .table-info td { padding: 3px 0; }
        .table-items th { 
            border-top: 1px dashed #000; 
            border-bottom: 1px dashed #000; 
            padding: 6px 0; 
            text-align: left; 
        }
        .table-items td { padding: 6px 0; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .btn-print-box { max-width: 550px; margin: 10px auto; text-align: right; }
        .btn-print { background: #ffc107; color: #000; padding: 6px 15px; border: 1px solid #ccc; cursor: pointer; font-weight: bold; border-radius: 4px; }
        @media print { .btn-print-box { display: none; } body { padding: 0; } .invoice-container { border: none; } }
    </style>
</head>
<body>

<div class="btn-print-box">
    <button class="btn-print" onclick="window.print()">🖨️ CETAK INVOICE (PRINT)</button>
</div>

<div class="invoice-container">
    <div class="header">
        <h2>PERCETAKAN STORE-CETAK</h2>
        <p>Sistem Informasi Manajemen Percetakan</p>
    </div>

    <div class="dashed-line"></div>

    <table class="table-info">
        <tr>
            <td width="30%">No. Invoice</td>
            <td width="3%">:</td>
            <td>
                <span class="text-bold">
                    <?php 
                    // Mengubah ID 1 menjadi 0001, ID 12 menjadi 0012, dst.
                    $nomor_urut = str_pad($invoice[0]['id_pesanan'], 4, '0', STR_PAD_LEFT);
                    $bulan = date('m', strtotime($invoice[0]['tgl_pesan']));
                    $tahun = date('Y', strtotime($invoice[0]['tgl_pesan']));
                    
                    // Menggabungkan jadi format unik: 0001/CR/SIM/07/2026
                    echo $nomor_urut . '/CR/SIM/' . $bulan . '/' . $tahun;
                    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>:</td>
            <td><?= $invoice[0]['nama_pelanggan']; ?></td>
        </tr>
        <tr>
            <td>Tanggal Transaksi</td>
            <td>:</td>
            <td><?= date('d-m-Y H:i', strtotime($invoice[0]['tgl_pesan'])); ?></td>
        </tr>
        <tr>
    <td>Status Bayar</td>
    <td>:</td>
    <td><span class="text-bold">[ <?= strtoupper($invoice[0]['status_bayar']); ?> ]</span></td>
</tr>
<tr>
    <td>Jatuh Tempo (Term)</td>
<td>:</td>
<td>
    <span class="text-bold">
        <?php 
        // Potong jalur: Langsung cek apakah kolom jatuh_tempo di database terisi tanggal valid
        if (!empty($invoice[0]['jatuh_tempo']) && $invoice[0]['jatuh_tempo'] != '0000-00-00' && $invoice[0]['jatuh_tempo'] != '1970-01-01') {
            echo date('d-m-Y', strtotime($invoice[0]['jatuh_tempo']));
        } else {
            echo '- (Langsung Lunas)';
        }
        ?>
    </span>
</td>
</tr>
    </table>

    <div class="dashed-line"></div>

    <table class="table-items">
        <thead>
            <tr>
                <th width="45%">Nama Produk</th>
                <th width="20%" class="text-right">Harga</th>
                <th width="15%" class="text-right">Qty</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($invoice as $item) : 
                $subtotal = $item['harga_jual'] * $item['jumlah_beli'];
            ?>
            <tr>
                <td><?= $item['nama_produk']; ?></td>
                <td class="text-right">Rp <?= number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                <td class="text-right"><?= $item['jumlah_beli']; ?></td>
                <td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            
            <tr>
                <td colspan="3" class="text-right text-bold" style="border-top: 1px dashed #000; padding-top: 10px;">GRAND TOTAL:</td>
                <td class="text-right text-bold" style="border-top: 1px dashed #000; padding-top: 10px;">Rp <?= number_format($invoice[0]['total_harga'], 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="dashed-line" style="margin-top: 25px;"></div>
    
    <div style="text-align: center; font-size: 12px; margin-top: 15px;">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
        </div>
</div>

</body>
</html>