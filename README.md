# 🖨️ SIM-Cetak (Sistem Informasi Manajemen Percetakan)
Aplikasi Tata Kelola Operasional, Kasir POS Multi-Item, Pengeluaran Toko, dan Dashboard Keuangan Real-Time Berbasis Web Menggunakan Framework CodeIgniter 3.

---

## 📌 Deskripsi Proyek
**SIM-Cetak** adalah platform Sistem Informasi Manajemen yang dirancang khusus untuk digitalisasi operasional UMKM di industri percetakan (*digital printing*, sablon, dan *offset*). Sistem ini mengintegrasikan seluruh alur kerja bisnis percetakan—mulai dari pengelolaan stok bahan baku, pencatatan biaya operasional, sistem kasir inventaris dinamis, kredit kontrol termin, hingga penyusunan laporan laba-rugi otomatis.

Aplikasi ini mengadopsi **Standar Akuntansi Manufaktur** untuk menyajikan margin keuntungan yang valid dan akurat, serta dilengkapi dengan fitur **Preventive Credit Control** guna mengamankan stabilitas arus kas (*cash flow*) dari risiko piutang macet.

---

## 🚀 Fitur Utama Sistem (End-to-End Modules)

### 1. 📁 Manajemen Produk & Bahan Baku (Inventory)
* **Mastering Aset Lancar:** Pencatatan komprehensif data bahan cetak (Kertas, Tinta, Banner, Vinil, Akrilik) lengkap dengan nama, jumlah stok, dan konversi satuan (*Pcs, Meter, Lembar*).
* **Dual-Pricing Engine:** Memisahkan secara tegas antara **Harga Beli (HPP/Modal dari Supplier)** dan **Harga Jual (ke Konsumen)** pada tiap produk untuk kalkulasi keuntungan yang presisi.
* **Auto-Deduct Stock:** Pengurangan stok bahan baku secara otomatis dan *real-time* sesaat setelah kasir menekan tombol simpan transaksi.

### 2. 🛒 Sistem Kasir POS & Manajemen Pesanan (Multi-Item)
* **Dynamic Multi-Item Order:** Kasir dapat menginput banyak item produk/jasa cetak sekaligus dalam satu nomor invoice pesanan secara fleksibel.
* **Sistem Pembayaran Fleksibel:** Mendukung opsi pembayaran tunai (**Lunas**) maupun pembayaran berjangka (**Belum Lunas / Termin**).
* **Smart Credit Control (Proteksi Piutang):** Sistem secara otomatis memindai rekam jejak digital pelanggan. Jika pelanggan memiliki nota gantung yang statusnya belum lunas dan telah melewati batas tanggal **Jatuh Tempo (Overdue)**, kasir otomatis **memblokir** pembuatan transaksi piutang baru atas nama pelanggan tersebut sampai utang lamanya diselesaikan.
* **Sistem Antrean Produksi:** Tracking status pengerjaan cetak menggunakan indikator dinamis: *Antri ➡️ Proses ➡️ Selesai*.

### 3. 💸 Pengelolaan Pengeluaran Toko (Operational Expenses)
* **Pencatatan Biaya Rutin:** Modul khusus untuk mencatat pengeluaran kas di luar belanja bahan baku, seperti biaya token listrik, gaji karyawan, perawatan mesin cetak, sewa gedung, hingga biaya operasional tidak terduga.
* **Kategorisasi Pengeluaran:** Memisahkan beban biaya harian agar tidak bercampur dengan belanja modal stok, guna menghindari distorsi pada laporan keuangan akhir bulan.

### 4. 📊 Dashboard Finansial & Laporan Laba-Rugi (Akuntansi Manufaktur)
Menampilkan visualisasi data ringkas berupa 4 indikator utama yang dihitung secara real-time:
* **Omzet (Pendapatan):** Akumulasi total nilai pesanan yang pembayarannya telah dinyatakan **Lunas**.
* **HPP (Harga Pokok Penjualan):** Total nilai intrinsik dari bahan baku yang *benar-benar terpakai* dalam produksi (dihitung dari: `Jumlah Beli x Harga Beli Produk`). Belanja stok yang mengendap di gudang tidak akan memotong omzet.
* **Laba Kotor (Gross Profit):** Pendapatan murni dari selisih penjualan produk sebelum dikurangi beban operasional (`Omzet - HPP`).
* **Laba Bersih (Net Profit):** Representasi profitabilitas asli toko setelah dikurangi biaya operasional (`Laba Kotor - Total Pengeluaran Toko`).

---

## 🛠️ Arsitektur Teknologi
* **Backend:** PHP 7.4 / 8.x dengan Framework **CodeIgniter 3** (Pola Desain MVC)
* **Database:** MySQL / MariaDB (Menerapkan Relasi Foreign Key & Cascade Delete untuk Integritas Data)
* **Frontend:** HTML5, CSS3, JavaScript (jQuery & AJAX)
* **UI Template:** **AdminLTE v4** Berbasis Bootstrap 4 & FontAwesome Icons

---

## 🗄️ Relasi Tabel Utama (Skema Database)
Aplikasi ini ditopang oleh 4 tabel utama yang saling berintegrasi:
1. `tb_produk`: Menjaga data stok dan struktur harga ganda (beli & jual).
2. `tb_pesanan`: Menyimpan data utama nota invoice, nama pelanggan, status bayar, dan tanggal jatuh tempo.
3. `tb_detail_pesanan`: Tabel jembatan (*junction table*) untuk menampung komparasi komoditas barang *multi-item* per transaksi.
4. `tb_pengeluaran`: Record data finansial pengeluaran kas operasional toko.

---

## 📄 Lampiran Dokumen
Seluruh berkas dokumen karya tulis ilmiah, visualisasi skema database, analisis sistem berjalan vs usulan, serta matriks pengujian aplikasi (*Black-Box Testing*) telah dibundel secara resmi ke dalam file Word: **`Laporan_Pengembangan_SIM_Cetak.docx`** yang berada di dalam direktori utama proyek ini.
