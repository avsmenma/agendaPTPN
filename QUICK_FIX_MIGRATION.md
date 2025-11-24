# 🔧 Quick Fix Migration Error di Server

## Masalah
Migration `2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table` masih error karena kolom `status_pembayaran` belum ada.

## Solusi Cepat

### **STEP 1: Cek Status Migration di Server**

Jalankan di server:
```bash
php artisan migrate:status
```

Cek apakah migration `2025_11_23_232000_add_pembayaran_fields_to_dokumens_table` sudah ada di list.

### **STEP 2: Rollback Migration yang Gagal**

```bash
# Rollback 1 step (migration yang gagal)
php artisan migrate:rollback --step=1
```

### **STEP 3: Pastikan File Migration Sudah Update**

Cek apakah file migration sudah benar:
```bash
cat database/migrations/2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table.php
```

Pastikan ada kode seperti ini:
```php
if (Schema::hasColumn('dokumens', 'status_pembayaran')) {
    $table->text('link_bukti_pembayaran')->nullable()->after('status_pembayaran');
} else {
    $table->text('link_bukti_pembayaran')->nullable();
}
```

### **STEP 4: Cek Apakah Migration 2025_11_23_232000 Sudah Ada di Database**

Masuk ke MySQL:
```bash
mysql -u your_username -p your_database_name
```

Cek tabel migrations:
```sql
SELECT * FROM migrations WHERE migration LIKE '%pembayaran%' ORDER BY id;
```

Jika migration `2025_11_23_232000_add_pembayaran_fields_to_dokumens_table` TIDAK ada, lanjut ke STEP 5.

Jika sudah ada tapi migration masih error, lanjut ke STEP 6.

### **STEP 5: Tambahkan Kolom Secara Manual (Jika Migration Belum Ada di Tabel)**

Jika migration `2025_11_23_232000` belum ada di tabel migrations, tambahkan kolom secara manual:

```sql
-- Masuk ke MySQL
mysql -u your_username -p your_database_name

-- Tambahkan kolom
ALTER TABLE dokumens 
ADD COLUMN sent_to_pembayaran_at TIMESTAMP NULL,
ADD COLUMN status_pembayaran ENUM('siap_dibayar', 'sudah_dibayar') NULL;

-- Tandai migration sudah dijalankan
INSERT INTO migrations (migration, batch) 
VALUES ('2025_11_23_232000_add_pembayaran_fields_to_dokumens_table', 
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS m));
```

### **STEP 6: Jalankan Migration Lagi**

Setelah kolom `status_pembayaran` sudah ada, jalankan migration:
```bash
php artisan migrate --force
```

---

## Alternatif: Fix Langsung di Server

Jika masih error, edit file migration langsung di server:

```bash
# Edit file migration
nano database/migrations/2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table.php
```

Ganti method `up()` dengan:

```php
public function up(): void
{
    Schema::table('dokumens', function (Blueprint $table) {
        // Check if column exists before adding
        if (!Schema::hasColumn('dokumens', 'link_bukti_pembayaran')) {
            // Try to add after status_pembayaran if it exists, otherwise just add at the end
            if (Schema::hasColumn('dokumens', 'status_pembayaran')) {
                $table->text('link_bukti_pembayaran')->nullable()->after('status_pembayaran');
            } else {
                $table->text('link_bukti_pembayaran')->nullable();
            }
        }
    });
}
```

Simpan (Ctrl+O, Enter, Ctrl+X), lalu jalankan migration lagi:
```bash
php artisan migrate --force
```

---

## Verifikasi

Setelah migration berhasil, verifikasi:

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

