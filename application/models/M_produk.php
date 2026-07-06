<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_produk extends CI_Model {

    // Fungsi untuk mengambil semua data produk dari database
    public function get_all_produk() {
        return $this->db->get('tb_produk')->result_array();
    }

    // Fungsi untuk menyimpan produk baru ke database
    public function simpan_produk($data) {
        // Pastikan nama array key sesuai dengan yang dikirim dari controller di Langkah 2
        $data_insert = [
            'nama_produk' => $data['nama_produk'],
            'stok'        => $data['stok'],
            'satuan'      => $data['satuan'],
            'harga_beli'  => $data['harga_beli'],
            'harga_jual'  => $data['harga_jual']
        ];
        return $this->db->insert('tb_produk', $data_insert);
    }
    // Fungsi untuk menyimpan data transaksi induk
    public function simpan_pesanan($data_pesanan) {
        $this->db->insert('tb_pesanan', $data_pesanan);
        return $this->db->insert_id(); // Mengembalikan ID pesanan yang baru saja dibuat
    }

    // Fungsi untuk menyimpan detail item yang dibeli
    public function simpan_detail_pesanan($data_detail) {
        $this->db->insert('tb_detail_pesanan', $data_detail);
    }

    // LOGIKA UTAMA: Fungsi untuk memotong stok produk otomatis
    public function kurangi_stok($id_produk, $jumlah_beli) {
        $this->db->set('stok', 'stok - ' . (int)$jumlah_beli, FALSE);
        $this->db->where('id_produk', $id_produk);
        $this->db->update('tb_produk');
    }
    // Fungsi untuk mengubah status produksi pesanan
    public function update_status_produksi($id_pesanan, $status_baru) {
        $this->db->where('id_pesanan', $id_pesanan);
        return $this->db->update('tb_pesanan', array('status_produksi' => $status_baru));
    }
    // Fungsi untuk menghitung total pemasukan dari pesanan yang LUNAS
    public function get_total_pemasukan() {
        $this->db->select_sum('total_harga');
        $this->db->where('status_bayar', 'Lunas');
        $query = $this->db->get('tb_pesanan');
        
        // Mengembalikan hasil penjumlahan (jika masih kosong, otomatis return 0)
        return $query->row()->total_harga ?? 0;
    }
    // Fungsi untuk mengubah status pembayaran menjadi Lunas
    public function update_status_bayar($id_pesanan) {
        $this->db->where('id_pesanan', $id_pesanan);
        return $this->db->update('tb_pesanan', array('status_bayar' => 'Lunas'));
    }
    // 1. LOGIKA KEMBALIKAN STOK: Dipakai saat pesanan dibatalkan/dihapus
    public function kembalikan_stok($id_produk, $jumlah_beli) {
        $this->db->set('stok', 'stok + ' . (int)$jumlah_beli, FALSE);
        $this->db->where('id_produk', $id_produk);
        $this->db->update('tb_produk');
    }

    // 2. LOGIKA HAPUS DATA: Menghapus data di tb_pesanan
    public function hapus_pesanan($id_pesanan) {
        $this->db->where('id_pesanan', $id_pesanan);
        return $this->db->delete('tb_pesanan');
    }

    // 3. LOGIKA UPDATE STOK MANUAL: Untuk halaman produk
    public function update_stok_manual($id_produk, $stok_baru) {
        $this->db->where('id_produk', $id_produk);
        return $this->db->update('tb_produk', array('stok' => $stok_baru));
    }
    public function get_all_pengeluaran() {
        return $this->db->order_by('id_pengeluaran', 'DESC')->get('tb_pengeluaran')->result_array();
    }

    // Menyimpan data pengeluaran baru
    public function simpan_pengeluaran($data) {
        return $this->db->insert('tb_pengeluaran', $data);
    }

    // Menghitung total semua pengeluaran untuk dashboard
    public function get_total_pengeluaran() {
        $this->db->select_sum('nominal');
        $query = $this->db->get('tb_pengeluaran');
        return $query->row()->nominal ?? 0;
    }
    // Mengambil total pendapatan kotor dari pesanan yang LUNAS
    public function get_total_pendapatan() {
        $this->db->select_sum('total_harga');
        $this->db->where('status_bayar', 'Lunas');
        $query = $this->db->get('tb_pesanan');
        return $query->row()->total_harga ?? 0;
    }

    // LOGIKA BARU: Menghitung Total Nilai Bahan Baku Terpakai (HPP)
    // Diambil dari: (jumlah_beli * harga_beli bahan baku) pada pesanan yang LUNAS
    public function get_total_bahan_terpakai() {
        $this->db->select('tb_detail_pesanan.jumlah_beli, tb_produk.harga_beli');
        $this->db->from('tb_detail_pesanan');
        $this->db->join('tb_produk', 'tb_detail_pesanan.id_produk = tb_produk.id_produk');
        $this->db->join('tb_pesanan', 'tb_detail_pesanan.id_pesanan = tb_pesanan.id_pesanan');
        
        // PASTIKAN BARIS INI SUDAH DIPERBAIKI:
        $this->db->where('tb_pesanan.status_bayar', 'Lunas');
        
        $query = $this->db->get()->result_array();

        $total_hpp = 0;
        foreach ($query as $row) {
            $total_hpp += ($row['jumlah_beli'] * $row['harga_beli']);
        }
        return $total_hpp;
    }
    // ==========================================
    // LOGIKA FILTER LAPORAN KEUANGAN (NEW)
    // ==========================================

    // Mengambil total omzet pesanan LUNAS berdasarkan rentang tanggal
    public function get_pendapatan_by_date($tgl_awal, $tgl_akhir) {
        $this->db->select_sum('total_harga');
        $this->db->where('status_bayar', 'Lunas');
        $this->db->where('tgl_pesan >=', $tgl_awal . ' 00:00:00');
        $this->db->where('tgl_pesan <=', $tgl_akhir . ' 23:59:59');
        $query = $this->db->get('tb_pesanan');
        return $query->row()->total_harga ?? 0;
    }

    // Mengambil total nilai bahan baku terpakai (HPP) berdasarkan rentang tanggal
    public function get_bahan_terpakai_by_date($tgl_awal, $tgl_akhir) {
        $this->db->select('tb_detail_pesanan.jumlah_beli, tb_produk.harga_beli');
        $this->db->from('tb_detail_pesanan');
        $this->db->join('tb_produk', 'tb_detail_pesanan.id_produk = tb_produk.id_produk');
        $this->db->join('tb_pesanan', 'tb_detail_pesanan.id_pesanan = tb_pesanan.id_pesanan');
        $this->db->where('tb_pesanan.status_bayar', 'Lunas');
        $this->db->where('tb_pesanan.tgl_pesan >=', $tgl_awal . ' 00:00:00');
        $this->db->where('tb_pesanan.tgl_pesan <=', $tgl_akhir . ' 23:59:59');
        $query = $this->db->get()->result_array();

        $total_hpp = 0;
        foreach ($query as $row) {
            $total_hpp += ($row['jumlah_beli'] * $row['harga_beli']);
        }
        return $total_hpp;
    }

    // Mengambil total biaya operasional berdasarkan rentang tanggal
    public function get_operasional_by_date($tgl_awal, $tgl_akhir) {
        $this->db->select_sum('nominal');
        $this->db->where('tgl_pengeluaran >=', $tgl_awal . ' 00:00:00');
        $this->db->where('tgl_pengeluaran <=', $tgl_akhir . ' 23:59:59');
        $query = $this->db->get('tb_pengeluaran');
        return $query->row()->nominal ?? 0;
    }
    // ==========================================
    // LOGIKA PEMBELIAN STOK BAHAN BAKU (NEW)
    // ==========================================

   public function beli_bahan_baku($id_produk, $jumlah, $harga_satuan, $harga_jual_baru) {
        $total_biaya = $jumlah * $harga_satuan;

        $data_pembelian = [
            'id_produk'          => $id_produk,
            'jumlah_beli'        => $jumlah,
            'harga_beli_satuan'  => $harga_satuan,
            'total_biaya'        => $total_biaya
        ];
        $this->db->insert('tb_pembelian_bahan', $data_pembelian);

        $produk = $this->db->get_where('tb_produk', ['id_produk' => $id_produk])->row_array();
        $stok_baru = $produk['stok'] + $jumlah;

        $this->db->where('id_produk', $id_produk);
        return $this->db->update('tb_produk', [
            'stok'        => $stok_baru,
            'harga_beli'  => $harga_satuan,
            'harga_jual'  => $harga_jual_baru
        ]);
    }
    // PASTIKAN FUNGSI INI ADA DI DALAM CLASS M_produk
    public function update_produk($id_produk, $data) {
        $this->db->where('id_produk', $id_produk);
        return $this->db->update('tb_produk', $data);
    }
    public function __construct() {
    parent::__construct();
    $this->load->model('M_produk'); // Pastikan baris load model ini ada
}
public function hapus_produk($id) {
    $this->db->where('id_produk', $id);
    return $this->db->delete('tb_produk'); // Menghapus data secara permanen dari tb_produk
}
}

    
