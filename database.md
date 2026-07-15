# Database Schema - IT Inventory System

Berikut adalah dokumentasi struktur tabel database (berdasarkan *migrations* terakhir) yang digunakan dalam aplikasi IT Inventory System.

## 1. `users`
Menyimpan data otentikasi administrator atau staf IT yang dapat login ke sistem.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `name` | `string` | Nama pengguna |
| `email` | `string` | Alamat email (Unique) |
| `email_verified_at` | `timestamp` | Waktu verifikasi email (Nullable) |
| `password` | `string` | Kata sandi yang di-*hash* |
| `vault_pin` | `string` | PIN 6 digit (hashed) untuk keamanan ekstra (Nullable) |
| `remember_token` | `string` | Token sesi remember me |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 2. `departments`
Menyimpan data departemen dan unit kerja dari masing-masing *user* PC.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `name` | `string` | Nama departemen (Composite Unique dengan unit) |
| `unit` | `string` | Nama unit kerja (Nullable, Composite Unique dengan name) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 3. `inventories`
Tabel utama untuk menyimpan data inventaris PC, alamat IP, serta kredensial *remote* pengguna.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `nama_user` | `string` | Nama pengguna PC |
| `computer_name` | `string` | Nama/Hostname komputer (Nullable) |
| `id_karyawan` | `string` | Nomor ID/NIK Karyawan (Nullable) |
| `username_ad` | `string` | Username Active Directory (Nullable) |
| `nomor_asset_pc` | `string` | Nomor aset fisik PC (Nullable) |
| `department_id` | `foreignId` | Foreign Key ke tabel `departments` |
| `ip_address` | `string` | Alamat IP Perangkat (Unique) |
| `password_remote` | `text` | Kata sandi *remote* yang dienkripsi (Nullable) |
| `notes` | `text` | Catatan tambahan (Nullable) |
| `created_by` | `foreignId` | User ID staf IT yang membuat data ini (Nullable) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 4. `activity_logs`
Merekam riwayat aktivitas sistem untuk keperluan *audit trail* (siapa melakukan apa).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `user_id` | `foreignId` | ID User yang melakukan aksi (Nullable) |
| `action` | `string` | Jenis aksi (contoh: *create*, *update*, *delete*, *login*) |
| `description` | `text` | Deskripsi rincian aksi yang dilakukan |
| `ip_address` | `string` | Alamat IP perangkat yang digunakan user (Nullable) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 5. `panels`
Menyimpan data rak/panel jaringan (*Network Panel*).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `name` | `string` | Nama Panel / Rak |
| `location` | `string` | Lokasi fisik panel (Nullable) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 6. `data_switches`
Menyimpan daftar Switch jaringan yang dapat dikelompokkan ke dalam suatu Panel.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `panel_id` | `foreignId` | Foreign Key ke tabel `panels` (Nullable) |
| `merk` | `string` | Merk/Brand Switch |
| `ip_address` | `string` | Alamat IP Switch (Unique) |
| `notes` | `text` | Catatan teknis (Nullable) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---

## 7. `topologies`
Menyimpan hasil desain dari fitur *Topology Designer* dalam format JSON mentah.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto Increment |
| `name` | `string` | Nama rancangan topologi |
| `date` | `date` | Tanggal perancangan topologi |
| `data` | `json` | Kumpulan seluruh node, panel, & garis (Nullable) |
| `created_at`, `updated_at` | `timestamp` | Tanggal data dibuat & diperbarui |

---
*(Catatan: Terdapat juga tabel sistem bawaan Laravel seperti `cache`, `jobs`, `job_batches`, `failed_jobs`, dan `sessions` yang mengelola antrean dan sesi di balik layar).*
