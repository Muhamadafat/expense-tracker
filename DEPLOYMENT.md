# 🚀 Panduan Deploy Expense Tracker ke Railway.app

## Persiapan

### 1. Buat Akun Railway
1. Buka [railway.app](https://railway.app)
2. Klik "Start a New Project"
3. Login dengan GitHub account kamu

### 2. Push Code ke GitHub
Pastikan code sudah di push ke GitHub repository:
```bash
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

## Langkah Deploy

### Step 1: Buat Project Baru di Railway

1. Login ke [railway.app](https://railway.app)
2. Klik **"New Project"**
3. Pilih **"Deploy from GitHub repo"**
4. Pilih repository **"expense-tracker"**
5. Railway akan otomatis detect PHP dan mulai deploy

### Step 2: Tambahkan MySQL Database

1. Di Railway dashboard project kamu, klik **"+ New"**
2. Pilih **"Database"** → **"Add MySQL"**
3. Railway akan otomatis membuat MySQL instance
4. **Database credentials akan otomatis tersedia sebagai environment variables**

### Step 3: Import Database Schema

1. Di Railway dashboard, klik service **MySQL**
2. Klik tab **"Data"** atau **"Connect"**
3. Copy connection string atau gunakan Railway CLI
4. Import file `database.sql`:

**Cara 1: Menggunakan Railway CLI**
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link project
railway link

# Connect ke database
railway connect mysql

# Setelah masuk MySQL shell, jalankan:
source database.sql;
# atau
USE <database_name>;
# lalu copy-paste isi database.sql
```

**Cara 2: Menggunakan MySQL Client**
```bash
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < database.sql
```

Ganti placeholder dengan credentials dari Railway:
- Klik MySQL service → Tab "Variables"
- Copy nilai `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`

### Step 4: Verifikasi Environment Variables

Railway akan otomatis set environment variables untuk MySQL:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

File `config.php` sudah dikonfigurasi untuk membaca environment variables ini.

### Step 5: Generate Domain & Test

1. Di Railway dashboard, klik service **expense-tracker** (bukan MySQL)
2. Klik tab **"Settings"**
3. Scroll ke **"Networking"**
4. Klik **"Generate Domain"**
5. Railway akan generate URL seperti: `expense-tracker-production-xxxx.up.railway.app`
6. Buka URL tersebut di browser
7. Aplikasi kamu seharusnya sudah live!

## Troubleshooting

### Aplikasi tidak bisa connect ke database

1. Pastikan MySQL service sudah running
2. Cek environment variables di service PHP:
   - Klik service PHP → Tab "Variables"
   - Pastikan ada `MYSQLHOST`, `MYSQLUSER`, etc
   - Jika tidak ada, Railway mungkin belum link otomatis

**Fix:** Link database secara manual
   - Klik service PHP → Tab "Variables"
   - Klik "Add Reference" → Pilih MySQL service
   - Pilih variabel yang diperlukan

### Error "Table doesn't exist"

Database belum di-import. Ulangi **Step 3**.

### Port error

Railway secara otomatis set environment variable `$PORT`. File `nixpacks.toml` sudah dikonfigurasi untuk menggunakan `$PORT`.

### PHP version issues

Edit `nixpacks.toml` jika perlu ganti PHP version:
```toml
[phases.setup]
nixPkgs = ["php82", "php82Extensions.mysqli", "php82Extensions.pdo", "php82Extensions.pdo_mysql"]
```

Ganti `php82` dengan `php81` atau `php83` jika diperlukan.

## Monitoring & Logs

1. Klik service PHP di Railway dashboard
2. Klik tab **"Deployments"** untuk lihat deployment history
3. Klik tab **"Logs"** untuk lihat application logs
4. Jika ada error, akan muncul di sini

## Update/Redeploy

Setiap kali kamu push ke GitHub, Railway akan otomatis redeploy:

```bash
git add .
git commit -m "Update feature"
git push origin main
```

Railway akan otomatis detect changes dan redeploy.

## Biaya

Railway free tier:
- **$5 credit per bulan**
- Cukup untuk small apps
- Jika habis, app akan sleep sampai bulan berikutnya

## Custom Domain (Optional)

1. Di Railway service PHP → Settings → Networking
2. Klik "Custom Domain"
3. Masukkan domain kamu (misal: `expense.example.com`)
4. Update DNS records sesuai instruksi Railway

---

## Checklist Deploy

- [ ] Push code ke GitHub
- [ ] Buat project baru di Railway
- [ ] Tambahkan MySQL database
- [ ] Import database.sql
- [ ] Generate domain
- [ ] Test aplikasi di browser
- [ ] Verifikasi bisa tambah/hapus expense

## Support

Jika ada masalah:
- [Railway Docs](https://docs.railway.app)
- [Railway Discord](https://discord.gg/railway)
- Cek logs di Railway dashboard

Selamat! Aplikasi kamu seharusnya sudah live di internet 🎉
