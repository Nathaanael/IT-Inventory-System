# Rancangan Menu Sidebar & Fitur Utama

Berdasarkan permasalahan utama yang Anda sampaikan—yaitu memindahkan **pencatatan IP User dan Password Remote dari file Excel** ke sistem web untuk meningkatkan keamanan dan kemudahan *tracking* tanpa perlu sistem *approval*—berikut adalah rancangan menu pada *Sidebar* beserta penjelasannya:

## 1. 📊 Dashboard
**Tujuan:** Memberikan ringkasan cepat (*overview*) mengenai keadaan data inventori saat ini.
**Isi Konten:**
- Total jumlah PC/User yang sudah terdata.
- Grafik sederhana departemen/divisi mana yang paling banyak di-remote.
- *Recent Activities* (Log aktivitas terbaru secara singkat, misalnya: "Nathan baru saja menambahkan data IP Finance").

## 2. 💻 Data Inventory (Menu Utama)
Ini adalah menu inti yang akan menggantikan file Excel Anda.
**Sub-menu yang disarankan:**
- **Daftar PC & Remote:** Halaman tabel (mirip Excel tapi lebih rapi) yang menampilkan kolom:
  - Nama User / Karyawan
  - Departemen
  - IP Address
  - ID Remote
  - Password Remote (Secara bawaan disensor dengan bintang `*****`. Ada tombol **"Show"** untuk melihatnya).
  *(Sesuai permintaan Anda sebelumnya, kolom "Aplikasi Remote" ditiadakan karena aplikasinya selalu tetap/sama).*
- **Logika Keamanan Utama:** Setiap kali seorang staf IT mengklik tombol **"Show"** pada password tertentu, aksi tersebut akan langsung direkam oleh sistem tanpa staf tersebut menyadarinya secara langsung (berjalan di *background* untuk keperluan audit).

## 3. 🛡️ Audit & Activity Logs
Karena kita menghilangkan sistem *approval* demi kemudahan staf IT bekerja cepat, maka **Audit Trail** adalah kunci keamanan penggantinya.
**Tujuan:** Melacak rekam jejak (*history*) penggunaan sistem.
**Isi Konten:**
- Tabel yang merekam siapa staf IT yang melakukan aksi, kapan, dan apa aksinya.
- Contoh rekaman log:
  - *IT Staff A melihat password remote PC Budi (Finance) pada 14:00.*
  - *IT SAS Supervisor menambahkan data IP untuk PC Siti (HRD) pada 10:00.*
- **Hak Akses:** Hanya bisa dilihat oleh *role* **Super Admin** (atau disesuaikan dengan kebutuhan pengawasan Anda).

## 4. ⚙️ Pengaturan & Master Data
**Tujuan:** Tempat untuk mengelola data pendukung dan akun staf IT.
**Sub-menu yang disarankan:**
- **Manajemen User (IT Staff):** Tempat Anda mendaftarkan atau mengelola akun untuk staf IT lainnya (ID Karyawan, Nama, Role, Password).
- **Master Departemen:** Untuk standarisasi nama divisi/departemen di tabel agar tidak ada *typo*.
- **Hak Akses:** Hanya bisa dilihat dan diatur oleh **Super Admin**.
