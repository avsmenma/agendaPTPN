# 🔧 Fix Migration Error: Column 'status_pembayaran' not found

## Masalah
Error terjadi saat menjalankan migration di server:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status_pembayaran' in 'dokumens'
```

## Penyebab
Migration `2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table` mencoba menambahkan kolom `link_bukti_pembayaran` setelah kolom `status_pembayaran`, tetapi kolom `status_pembayaran` belum ada di database production.

## Solusi

### **STEP 1: Pull Perubahan Terbaru dari GitHub**

Di server, jalankan:
```bash
cd /var/www/agenda_online_ptpn
git pull origin main
```

### **STEP 2: Rollback Migration yang Gagal (Jika Perlu)**

Jika migration sudah berjalan sebagian, rollback dulu:
```bash
php artisan migrate:rollback --step=1
```

### **STEP 3: Jalankan Migration Lagi**

Migration baru sudah dibuat dengan urutan yang benar:
- `2025_11_23_232000_add_pembayaran_fields_to_dokumens_table` (menambahkan `status_pembayaran` dan `sent_to_pembayaran_at`)
- `2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table` (menambahkan `link_bukti_pembayaran` setelah `status_pembayaran`)

Jalankan:
```bash
php artisan migrate --force
```

### **STEP 4: Verifikasi**

Cek apakah kolom sudah ditambahkan:
```bash
php artisan tinker
```

Di dalam tinker:
```php
Schema::hasColumn('dokumens', 'status_pembayaran');
Schema::hasColumn('dokumens', 'sent_to_pembayaran_at');
Schema::hasColumn('dokumens', 'link_bukti_pembayaran');
```

Semua harus return `true`.

## Alternatif: Manual Fix (Jika Migration Masih Error)

Jika masih ada masalah, tambahkan kolom secara manual:

```sql
-- Masuk ke MySQL
mysql -u your_username -p your_database_name

-- Tambahkan kolom status_pembayaran dan sent_to_pembayaran_at
ALTER TABLE dokumens 
ADD COLUMN sent_to_pembayaran_at TIMESTAMP NULL AFTER updated_at,
ADD COLUMN status_pembayaran ENUM('siap_dibayar', 'sudah_dibayar') NULL AFTER sent_to_pembayaran_at;

-- Tambahkan kolom link_bukti_pembayaran
ALTER TABLE dokumens 
ADD COLUMN link_bukti_pembayaran TEXT NULL AFTER status_pembayaran;

-- Update migration table untuk menandai migration sudah dijalankan
INSERT INTO migrations (migration, batch) VALUES 
('2025_11_23_232000_add_pembayaran_fields_to_dokumens_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS m)),
('2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS m));
```

## Setelah Fix

Setelah migration berhasil, lanjutkan deployment:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

