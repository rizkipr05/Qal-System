# QAL System

Sistem pengelolaan dokumen **Quality Acceptance Letter (QAL)** berbasis CodeIgniter 4.

## Alur Proses
1. Construction menyerahkan dokumen pendukung.
2. QC menelaah dokumen dan menyusun QAL.
3. PC menandatangani QAL.
4. Owner melakukan approval.
5. Dokumen kembali ke QC untuk arsip proyek.

Status dokumen yang digunakan:
- `draft`
- `submitted`
- `reviewed`
- `pc_signed`
- `revision_requested`
- `archived`

## Role & Hak Akses
- `construction`
  - Buat QAL
  - Upload dokumen awal dan revisi
  - Submit ke QC
- `qc`
  - Review dokumen dari Construction
  - Approve review (teruskan ke PC)
  - Minta revisi
- `pc`
  - Tanda tangan QAL
  - Kelola user untuk role: `construction`, `qc`, `owner`
- `owner`
  - Approval akhir
- `admin`
  - Kelola semua user dan activity logs

## Fitur Utama
- Dashboard dan tampilan aksi berbeda per role.
- Manajemen dokumen QAL end-to-end sesuai alur.
- Upload file multi-file:
  - Bisa pilih banyak file sekaligus.
  - Bisa tambah file bertahap (tidak menimpa pilihan sebelumnya).
  - Daftar file terpilih ditampilkan sebelum submit.
- Riwayat versi dokumen + download per versi.
- Format print QAL berbentuk surat/checklist dan blok tanda tangan.
- Profil pengguna:
  - Setiap user bisa edit nama, email, dan password sendiri.

## Tech Stack
- PHP 8.2+
- CodeIgniter 4.7
- MySQL/MariaDB
- Bootstrap 5

## Instalasi
1. Install dependency:
```bash
composer install
```
2. Copy env:
```bash
cp env .env
```
3. Atur `.env`:
- `app.baseURL`
- `database.default.hostname`
- `database.default.database`
- `database.default.username`
- `database.default.password`
- `database.default.port`

4. Buat database:
```sql
CREATE DATABASE qal_system;
```

5. Jalankan migrasi:
```bash
php spark migrate
```

6. Seed user awal:
```bash
php spark db:seed DcSeeder
```

7. Jalankan aplikasi:
```bash
php spark serve
```

## Akun Seed Default
Dari `app/Database/Seeds/DcSeeder.php`:
- `construction@example.com` / `construction`
- `qc@example.com` / `qc`
- `pc@example.com` / `pc`
- `owner@example.com` / `owner`

Catatan:
- Seeder default saat ini tidak membuat akun `admin`.
- Buat admin dari menu manajemen user (oleh admin existing) atau insert manual ke tabel `users`.

## Struktur Database (Ringkas)
- `users`
- `documents`
- `document_versions`
- `document_reviews`
- `activity_logs`

Migrasi ada di `app/Database/Migrations/`.

## Rute Utama
- `GET /login`
- `GET /dc`
- `GET /dc/profile`
- `GET /dc/create`
- `GET /dc/{id}`
- `POST /dc/{id}/submit`
- `POST /dc/{id}/review`
- `POST /dc/{id}/approve`
- `POST /dc/{id}/owner-approve`
- `GET /admin/users`

## Catatan Operasional
- Pastikan web server diarahkan ke folder `public/`.
- Jika muncul error koneksi DB, cek service MySQL dan konfigurasi `.env`.
- Jika ada perubahan schema baru, jalankan kembali:
```bash
php spark migrate
```
