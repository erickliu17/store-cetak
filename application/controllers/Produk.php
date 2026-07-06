<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load model M_produk agar bisa dipakai di semua fungsi di bawah
        $this->load->model('M_produk');
    }

    // Halaman Utama Produk (Tampil Data)
    public function index() {
        $data['produk'] = $this->M_produk->get_all_produk();
        $this->load->view('v_produk', $data);
    }

    // Fungsi Proses Tambah Data
    public function simpan() {
        // Ambil data dari name input HTML view
        $data = [
            'nama_produk' => $this->input->post('nama_produk'),
            'stok'        => $this->input->post('stok'),
            'satuan'      => $this->input->post('satuan'),
            'harga_beli'  => $this->input->post('harga_beli'), // Menangkap name="harga_beli"
            'harga_jual'  => $this->input->post('harga_jual')  // Menangkap name="harga_jual"
        ];

        $this->M_produk->simpan_produk($data);
        $this->session->set_flashdata('sukses', 'Produk baru berhasil ditambahkan!');
        redirect('produk');
    }
    public function update_stok() {
        $id_produk = $this->input->post('id_produk');
        $stok_baru = $this->input->post('stok_baru');

        $this->M_produk->update_stok_manual($id_produk, $stok_baru);
        redirect('produk');
    }

    public function proses_beli() {
        $id_produk       = $this->input->post('id_produk');
        $jumlah          = $this->input->post('jumlah_beli');
        $harga_satuan    = $this->input->post('harga_beli_satuan');
        $harga_jual_baru = $this->input->post('harga_jual_baru'); // Pastikan baris ini ADA

        if ($id_produk && $jumlah && $harga_satuan) {
            // KIRIM DATA LENGKAP: 4 argumen agar pas dengan Model
            $this->M_produk->beli_bahan_baku($id_produk, $jumlah, $harga_satuan, $harga_jual_baru);
            $this->session->set_flashdata('sukses', 'Stok berhasil direstock dan harga ter-update!');
        }
        
        redirect('produk');
    }

    public function update() {
    $id_produk   = $this->input->post('id_produk');
    
    $data = [   
        'nama_produk' => $this->input->post('nama_produk'),
        'stok'        => $this->input->post('stok'),
        'satuan'      => $this->input->post('satuan'),
        'harga_beli'  => $this->input->post('harga_beli'), // Mengubah Harga Modal
        'harga_jual'  => $this->input->post('harga_jual')  // Mengubah Harga Kasir
    ];

    if ($id_produk) {
        $this->M_produk->update_produk($id_produk, $data);
        $this->session->set_flashdata('sukses', 'Data produk berhasil diperbarui secara manual!');
    }
    
    redirect('produk');
}
public function hapus($id) {
    if ($id) {
        $this->M_produk->hapus_produk($id);
        $this->session->set_flashdata('sukses', 'Produk berhasil dihapus dari sistem gudang!');
    }
    redirect('produk');
}
}