# 🚀 Panduan Deploy ke 000webhost (GAMPANG!)

## Step 1: Daftar Akun 000webhost

1. Buka [www.000webhost.com](https://www.000webhost.com)
2. Klik **"Free Sign Up"**
3. Isi form pendaftaran
4. Verifikasi email
5. Login ke dashboard

---

## Step 2: Buat Website Baru

1. Di dashboard 000webhost, klik **"Create New Website"** atau **"Build Website"**
2. Pilih **"Create Empty Website"** (jangan pilih WordPress!)
3. Isi:
   - **Website Name**: `expense-tracker` (atau nama lain yang kamu suka)
   - **Password**: buat password untuk database
4. Klik **"Create"**
5. Tunggu beberapa detik sampai website siap

---

## Step 3: Upload File PHP

### Cara 1: Via File Manager (Recommended)

1. Di dashboard, klik website yang baru kamu buat
2. Klik **"File Manager"** atau **"Manage Website"** → **"File Manager"**
3. Masuk ke folder **`public_html`** (klik 2x)
4. **Hapus semua file default** (index.html, dll) - select all → Delete
5. Klik **"Upload Files"**
6. Upload file-file berikut dari folder `expense-tracker`:
   - ✅ `index.php`
   - ✅ `config.php`
   - ❌ JANGAN upload: `database.sql`, `.git/`, `DEPLOYMENT.md`, `nixpacks.toml`, `router.php`, `.htaccess`

### Cara 2: Via FTP (Opsional)

Kalau lebih suka pakai FileZilla:
1. Install FileZilla
2. FTP credentials ada di 000webhost dashboard → Settings → FTP
3. Connect & upload file ke folder `public_html`

---

## Step 4: Buat Database MySQL

1. Di dashboard 000webhost, klik website kamu
2. Klik **"Tools"** → **"Database Manager"** atau **"Manage Database"**
3. Klik **"New Database"**
4. Isi:
   - **Database name**: `id123456_expense_tracker` (000webhost otomatis kasih prefix)
   - **Database username**: otomatis terisi (biasanya sama dengan db name)
   - **Password**: buat password yang kuat
5. Klik **"Create Database"**
6. **CATAT credentials ini!** Nanti diperlukan untuk config.php

**Contoh credentials:**
```
Database Host: localhost (atau sql123.000webhost.com)
Database Name: id123456_expense_tracker
Database User: id123456_expense_tracker
Database Password: ******** (yang kamu buat tadi)
```

---

## Step 5: Import Database Schema

1. Masih di **Database Manager**, klik **"Manage"** atau **"phpMyAdmin"**
2. Klik database kamu di sidebar kiri
3. Klik tab **"SQL"** di atas
4. Copy-paste query berikut:

```sql
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_expense_date` (`expense_date`),
  KEY `idx_payment_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `expenses` (`expense_date`, `description`, `payment_method`, `amount`) VALUES
('2025-10-15', 'Makan siang bersama', 'Cash', 150000.00),
('2025-10-16', 'Belanja mingguan', 'E-Wallet', 350000.00),
('2025-10-18', 'Transport', 'Cash', 50000.00);
```

5. Klik **"Go"** atau **"Execute"**
6. Kalau muncul "Query OK", berarti berhasil!

---

## Step 6: Update config.php dengan Database Credentials

**PENTING!** Kamu perlu edit file `config.php` di 000webhost dengan credentials database yang baru.

### Via File Manager:

1. Buka **File Manager** → `public_html` → `config.php`
2. Klik kanan → **"Edit"** atau klik icon pensil
3. Ganti baris 24-27 dengan credentials dari Step 4:

```php
// Fallback untuk local development
define('DB_HOST', 'localhost'); // atau sql123.000webhost.com
define('DB_USER', 'id123456_expense_tracker'); // ganti dengan DB User kamu
define('DB_PASS', 'password_kamu_tadi'); // ganti dengan DB Password kamu
define('DB_NAME', 'id123456_expense_tracker'); // ganti dengan DB Name kamu
```

**Contoh setelah diedit:**
```php
// Fallback untuk local development
define('DB_HOST', 'localhost');
define('DB_USER', 'id123456_expense_tracker');
define('DB_PASS', 'mySecureP@ss123');
define('DB_NAME', 'id123456_expense_tracker');
```

4. Klik **"Save"**

---

## Step 7: Test Aplikasi! 🎉

1. Buka URL website kamu: `https://your-website.000webhostapp.com`
2. Aplikasi seharusnya sudah jalan!
3. Coba tambah expense baru
4. Coba filter berdasarkan bulan
5. Coba hapus expense

**Kalau muncul error "Koneksi gagal":**
- Cek lagi config.php, pastikan DB credentials benar
- Pastikan database sudah di-import (Step 5)

---

## Troubleshooting

### Error: "Koneksi gagal: Access denied"
→ Database credentials salah. Cek lagi DB_USER, DB_PASS, DB_NAME di config.php

### Error: "Table 'expenses' doesn't exist"
→ Database belum di-import. Ulangi Step 5

### Halaman blank/putih
→ PHP error. Cek error log di 000webhost dashboard atau tambahkan di config.php:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Website tidak bisa diakses
→ Tunggu beberapa menit. 000webhost kadang butuh waktu untuk propagasi DNS

---

## Custom Domain (Opsional)

Kalau punya domain sendiri:
1. Di 000webhost dashboard → **"Settings"** → **"General"**
2. Klik **"Park Domain"**
3. Ikuti instruksi untuk update DNS

---

## 🎉 Selesai!

Aplikasi kamu sekarang sudah live di internet dengan 000webhost!

**URL:** `https://your-website.000webhostapp.com`

Enjoy! 🚀
