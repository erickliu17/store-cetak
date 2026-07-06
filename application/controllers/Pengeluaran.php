<?php
defined('BASEPATH') OR exit('No direct access allowed');

class Pengeluaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_produk');
    }

    // Halaman Utama Modul Pengeluaran
    public function index() {
        $data['pengeluaran'] = $this->M_produk->get_all_pengeluaran();
        $this->load->view('v_pengeluaran', $data);
    }

    // Fungsi untuk menyimpan inputan pengeluaran baru
    public function simpan() {
        $data = array(
            'tgl_pengeluaran' => date('Y-m-d H:i:s'),
            'kategori'        => $this->input->post('kategori'),
            'keterangan'      => $this->input->post('keterangan'),
            'nominal'         => $this->input->post('nominal')
        );

        $this->M_produk->simpan_pengeluaran($data);
        redirect('pengeluaran');
    }
}