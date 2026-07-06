<?php
defined('BASEPATH') OR exit('No direct access allowed');

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_produk');
    }

    public function index() {
        // Ambil input tanggal dari form filter (jika ada)
        $tgl_awal  = $this->input->get('tgl_awal');
        $tgl_akhir = $this->input->get('tgl_akhir');

        // Jika user belum filter, default menampilkan data hari ini
        if (!$tgl_awal || !$tgl_akhir) {
            $tgl_awal  = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }

        // Panggil fungsi model berdasarkan tanggal terfilter
        $pendapatan     = $this->M_produk->get_pendapatan_by_date($tgl_awal, $tgl_akhir);
        $bahan_terpakai = $this->M_produk->get_bahan_terpakai_by_date($tgl_awal, $tgl_akhir);
        $operasional    = $this->M_produk->get_operasional_by_date($tgl_awal, $tgl_akhir);

        // Hitung matematika akuntansinya
        $laba_kotor  = $pendapatan - $bahan_terpakai;
        $laba_besih  = $laba_kotor - $operasional;

        // Ambil detail riwayat transaksi untuk ditampilkan di tabel laporan
        $data['pesanan_terfilter'] = $this->db->where('status_bayar', 'Lunas')
                                              ->where('tgl_pesan >=', $tgl_awal.' 00:00:00')
                                              ->where('tgl_pesan <=', $tgl_akhir.' 23:59:59')
                                              ->get('tb_pesanan')->result_array();

        // Oper data ke view
        $data['tgl_awal']        = $tgl_awal;
        $data['tgl_akhir']       = $tgl_akhir;
        $data['total_omzet']     = $pendapatan;
        $data['bahan_terpakai']  = $bahan_terpakai;
        $data['laba_kotor']      = $laba_kotor;
        $data['operasional']     = $operasional;
        $data['laba_bersih']     = $laba_besih;

        $this->load->view('v_laporan', $data);
    }
}