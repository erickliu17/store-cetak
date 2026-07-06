<?php
defined('BASEPATH') OR exit('No direct access allowed');

class Pesanan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_produk');
    }

    // Halaman Utama Kasir (Tampil Form Transaksi & Riwayat)
public function index() {
    // 1. Ambil data produk untuk dropdown input kasir
    $data['produk']  = $this->M_produk->get_all_produk();
    
    // 2. Ambil data riwayat semua transaksi (Urutan terbaru di atas)
    $data['pesanan'] = $this->db->order_by('id_pesanan', 'DESC')->get('tb_pesanan')->result_array();
    
    // 3. Ambil data finansial mentah dari Model M_produk
    $pendapatan     = $this->M_produk->get_total_pendapatan();
    $bahan_terpakai = $this->M_produk->get_total_bahan_terpakai(); // Ini HPP kamu
    $operasional    = $this->M_produk->get_total_pengeluaran();   // Dari tb_pengeluaran
    
    // 4. Hitung berdasarkan rumus akuntansi referensi skripsi
    $laba_kotor     = $pendapatan - $bahan_terpakai;
    $laba_bersih    = $laba_kotor - $operasional;
    
    // 5. Lempar data ke View v_pesanan agar bisa dipanggil ringkasannya
    $data['total_pemasukan']   = $pendapatan;
    $data['total_pengeluaran'] = $operasional;
    $data['laba_kotor']        = $laba_kotor;
    $data['saldo_kas']         = $laba_bersih; 
    
    // 6. Muat halaman tampilan utama
    $this->load->view('v_pesanan', $data);
}

    // Fungsi Logika Simpan Transaksi dan Potong Stok
    public function simpan() {
    $id_produk   = $this->input->post('id_produk');
    $jumlah_beli = $this->input->post('jumlah_beli');

    // 1. Ambil data produk untuk cross-check stok dan ambil harga jual terbaru
    $produk = $this->db->get_where('tb_produk', array('id_produk' => $id_produk))->row_array();
    
    // Keamanan Lapis Kedua: Jika Qty melebihi stok di gudang
    if (!$produk || $jumlah_beli > $produk['stok']) {
        $this->session->set_flashdata('error', 'Transaksi Gagal! Jumlah beli melebihi batas stok gudang.');
        redirect('pesanan');
        return;
    }

    // Hitung total harga jual untuk konsumen
    $total_harga = $produk['harga_jual'] * $jumlah_beli;

    // 2. Siapkan data untuk tabel induk (tb_pesanan)
    $data_pesanan = array(
        'nama_pelanggan'  => $this->input->post('nama_pelanggan'),
        'tgl_pesan'       => date('Y-m-d H:i:s'),
        'total_harga'     => $total_harga,
        'status_bayar'    => $this->input->post('status_bayar'),
        'status_produksi' => 'Antri'
    );

    // 3. Proses insert ke tabel induk (tb_pesanan)
    $this->db->insert('tb_pesanan', $data_pesanan);
    $id_pesanan_baru = $this->db->insert_id(); 

    // 🔥 PERBAIKAN TOTAL: Kita HANYA memasukkan kolom yang pasti ada di tb_detail_pesanan kamu
    $data_detail = array(
        'id_pesanan'  => $id_pesanan_baru,
        'id_produk'   => $id_produk,
        'jumlah_beli' => $jumlah_beli
        // Kolom harga sengaja kita buang dari sini agar BEBAS DARI ERROR 1054!
    );
    $this->db->insert('tb_detail_pesanan', $data_detail);

    // 4. Integrasi Pemotongan Stok Otomatis jika Status Bayar = Lunas
    if ($this->input->post('status_bayar') == 'Lunas') {
        $stok_baru = $produk['stok'] - $jumlah_beli;
        $this->db->where('id_produk', $id_produk);
        $this->db->update('tb_produk', array('stok' => $stok_baru));
    }

    $this->session->set_flashdata('sukses', 'Pesanan berhasil disimpan!');
    redirect('pesanan');
}
    // Fungsi untuk memproses perubahan status produksi
    public function update_status($id_pesanan, $status_baru) {
        // Panggil fungsi di model untuk update status
        $this->M_produk->update_status_produksi($id_pesanan, $status_baru);
        
        // Kembalikan ke halaman kasir pesanan setelah selesai
        redirect('pesanan');
    }
    // Fungsi untuk memproses aksi tombol Lunaskan
    public function bayar($id_pesanan) {
        // Panggil fungsi di model untuk mengubah status menjadi Lunas
        $this->M_produk->update_status_bayar($id_pesanan);
        
        // Kembalikan ke halaman kasir pesanan
        redirect('pesanan');
    }
    public function hapus($id_pesanan) {
        // A. Ambil detail pesanan dulu untuk tahu produk apa dan berapa jumlah yang dibeli kemarin
        $detail = $this->db->get_where('tb_detail_pesanan', array('id_pesanan' => $id_pesanan))->row_array();
        
        if ($detail) {
            // B. KEMBALIKAN STOK: Tambahkan kembali stok yang sempat terpotong
            $this->M_produk->kembalikan_stok($detail['id_produk'], $detail['jumlah_beli']);
        }

        // C. Hapus data transaksi (tb_detail_pesanan akan otomatis terhapus karena foreign key CASCADE kita di awal)
        $this->M_produk->hapus_pesanan($id_pesanan);

        redirect('pesanan');
    }
    public function get_total_hpp() {
    // Rumus SQL: Mengalikan Qty terjual dengan Harga Modal asli dari gudang
    $this->db->select('SUM(tb_detail_pesanan.jumlah_beli * tb_produk.harga_beli) AS total_hpp');
    $this->db->from('tb_detail_pesanan');
    $this->db->join('tb_produk', 'tb_detail_pesanan.id_produk = tb_produk.id_produk');
    $this->db->join('tb_pesanan', 'tb_detail_pesanan.id_pesanan = tb_pesanan.id_pesanan');
    
    // HPP hanya dihitung dari transaksi yang uangnya sudah masuk (Lunas)
    $this->db->where('tb_pesanan.status_bayar', 'Lunas');
    
    $query = $this->db->get();
    return $query->row()->total_hpp;
    
}
public function simpan_multi() {
    $nama_pelanggan   = $this->input->post('nama_pelanggan');
    $status_bayar     = $this->input->post('status_bayar');
    $jatuh_tempo      = $this->input->post('jatuh_tempo');
    $produk_keranjang = $this->input->post('produk_keranjang');

    if (empty($produk_keranjang)) {
        $this->session->set_flashdata('error', '⚠️ Transaksi gagal, keranjang belanja kosong!');
        redirect('pesanan');
        return;
    }

    // --- TAHAP 1: HITUNG TOTAL GRAND HARGA ---
    $grand_total = 0;
    foreach ($produk_keranjang as $item) {
        $prod = $this->db->get_where('tb_produk', array('id_produk' => $item['id_produk']))->row_array();
        $grand_total += ($prod['harga_jual'] * $item['jumlah_beli']);
    }

    // --- TAHAP 2: LOGIKA VALIDASI BLOKIR TERM & NOTIFIKASI DETAIL ---
    // 🔥 REVISI: Validasi blokir HANYA berjalan jika transaksi baru ini statusnya "Belum Lunas"
    if ($status_bayar == 'Belum Lunas') {
        $hari_ini = date('Y-m-d');
        
        $this->db->where('nama_pelanggan', $nama_pelanggan);
        $this->db->where('status_bayar', 'Belum Lunas');
        $this->db->where('jatuh_tempo <', $hari_ini); // Sudah melewati jatuh tempo
        $query_macet = $this->db->get('tb_pesanan');

        if ($query_macet->num_rows() > 0) {
            // Ambil data invoice pertama yang menunggak/macet
            $invoice_macet = $query_macet->row_array();
            
            // Format nomor invoice unik (0001/CR/SIM/06/2026)
            $no_urut = str_pad($invoice_macet['id_pesanan'], 4, '0', STR_PAD_LEFT);
            $bulan   = date('m', strtotime($invoice_macet['tgl_pesan']));
            $tahun   = date('Y', strtotime($invoice_macet['tgl_pesan']));
            $format_invoice = $no_urut . '/CR/SIM/' . $bulan . '/' . $tahun;
            
            $tgl_term = date('d-m-Y', strtotime($invoice_macet['jatuh_tempo']));

            // Pesan penolakan khusus karena mencoba berutang lagi
            $pesan_error = "⚠️ <strong>TRANSAKSI DITOLAK!</strong> Pelanggan <strong>" . $nama_pelanggan . "</strong> tidak boleh mengambil termin/utang lagi sebelum melunasi Invoice Nomor: <strong>" . $format_invoice . "</strong> (Jatuh tempo: " . $tgl_term . "). Silakan ubah status pembayaran menjadi <strong>Lunas</strong> jika ingin melanjutkan transaksi.";
            
            $this->session->set_flashdata('error', $pesan_error);
            redirect('pesanan');
            return; 
        }
    }
    // ===================================================================

    // --- TAHAP 3: INSERT KE tb_pesanan ---
    $data_pesanan = array(
        'nama_pelanggan'  => $nama_pelanggan,
        'tgl_pesan'       => date('Y-m-d H:i:s'),
        'total_harga'     => $grand_total,
        'status_bayar'    => $status_bayar,
        'jatuh_tempo'     => ($status_bayar == 'Belum Lunas' && !empty($jatuh_tempo)) ? $jatuh_tempo : null,
        'status_produksi' => 'Antri'
    );
    $this->db->insert('tb_pesanan', $data_pesanan);
    $id_pesanan_baru = $this->db->insert_id();

    // --- TAHAP 4: LOOPING DETAIL PESANAN & POTONG STOK ---
    foreach ($produk_keranjang as $item) {
        $id_produk   = $item['id_produk'];
        $jumlah_beli = $item['jumlah_beli'];

        $prod = $this->db->get_where('tb_produk', array('id_produk' => $id_produk))->row_array();

        $data_detail = array(
            'id_pesanan'  => $id_pesanan_baru,
            'id_produk'   => $id_produk,
            'jumlah_beli' => $jumlah_beli
        );
        $this->db->insert('tb_detail_pesanan', $data_detail);

        // Potong stok otomatis
        $stok_baru = $prod['stok'] - $jumlah_beli;
        $this->db->where('id_produk', $id_produk);
        $this->db->update('tb_produk', array('stok' => $stok_baru));
    }

    $this->session->set_flashdata('sukses', 'Multi-Transaksi Berhasil Disimpan!');
    redirect('pesanan');
}
public function cetak_invoice($id_pesanan) {
    // Pastikan query ini mengambil data dari tb_pesanan secara utuh
    $this->db->select('tb_pesanan.*, tb_detail_pesanan.*, tb_produk.nama_produk, tb_produk.harga_jual');
    $this->db->from('tb_pesanan');
    $this->db->join('tb_detail_pesanan', 'tb_detail_pesanan.id_pesanan = tb_pesanan.id_pesanan');
    $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_pesanan.id_produk');
    $this->db->where('tb_pesanan.id_pesanan', $id_pesanan);
    $data['invoice'] = $this->db->get()->result_array();

    $this->load->view('v_cetak_invoice', $data);
}
// FUNGSI 1: MENGUBAH STATUS BELUM LUNAS MENJADI LUNAS
public function set_lunas($id_pesanan) {
    $this->db->where('id_pesanan', $id_pesanan);
    $this->db->update('tb_pesanan', array('status_bayar' => 'Lunas'));

    $this->session->set_flashdata('sukses', 'Invoice #' . $id_pesanan . ' Berhasil Ditetapkan Lunas!');
    redirect('pesanan');
}

// FUNGSI 2: MENGUBAH STATUS PROSES PRODUKSI (Antri -> Proses -> Selesai)
public function update_status_produksi($id_pesanan) {
    $status_baru = $this->input->post('status_produksi');
    if(!empty($status_baru)) {
        $this->db->where('id_pesanan', $id_pesanan);
        $this->db->update('tb_pesanan', array('status_produksi' => $status_baru));
        $this->session->set_flashdata('sukses', 'Status Produksi Invoice #' . $id_pesanan . ' Berhasil Diperbarui!');
    }
    redirect('pesanan');
}

// FUNGSI 3: MENGHAPUS PESANAN UTAMA & DATA ANAK DI DETAIL PESANAN
public function hapus_pesanan($id_pesanan) {
    // Jalankan aturan ON DELETE CASCADE secara manual lewat query agar data aman berelasi
    // 1. Hapus data anak di tabel detail pesanan terlebih dahulu
    $this->db->where('id_pesanan', $id_pesanan);
    $this->db->delete('tb_detail_pesanan');

    // 2. Hapus data induk di tabel pesanan utama
    $this->db->where('id_pesanan', $id_pesanan);
    $this->db->delete('tb_pesanan');

    $this->session->set_flashdata('sukses', 'Invoice #' . $id_pesanan . ' beserta detail itemnya berhasil dihapus dari sistem!');
    redirect('pesanan');
}
// 1. Tampilan Halaman Daftar Piutang
public function piutang() {
    // Ambil semua pesanan yang statusnya 'Belum Lunas'
    $this->db->order_by('id_pesanan', 'DESC');
    $data['piutang'] = $this->db->get_where('tb_pesanan', array('status_bayar' => 'Belum Lunas'))->result_array();
    
    // HANYA LOAD VIEW PIUTANG SAJA, BYPASS JALUR LAIN!
    $this->load->view('v_piutang', $data);
}

// 2. Proses Aksi Mengubah Status Menjadi Lunas (Pelunasan)
public function lunaskan_nota($id_pesanan) {
    $this->db->where('id_pesanan', $id_pesanan);
    $this->db->update('tb_pesanan', array(
        'status_bayar' => 'Lunas',
        'jatuh_tempo'  => null // Bersihkan tanggal jatuh tempo karena sudah lunas
    ));

    $this->session->set_flashdata('sukses', '🎉 Pembayaran berhasil! Nota nomor ' . $id_pesanan . ' kini dinyatakan LUNAS.');
    redirect('pesanan/piutang');
}
}