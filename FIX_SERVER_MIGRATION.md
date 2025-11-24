# 🚨 Fix Migration Error di Server - Langkah Cepat

## Masalah
Migration error karena kolom `status_pembayaran` belum ada saat migration `link_bukti_pembayaran` mencoba menggunakannya.

## Solusi Cepat (Pilih Salah Satu)

### **Opsi 1: Fix Manual dengan SQL (TERCEPAT)**

Jalankan di server:

```bash
# Masuk ke MySQL
mysql -u your_username -p your_database_name
```

Kemudian jalankan SQL berikut:

```sql
-- 1. Tambahkan kolom yang hilang
ALTER TABLE dokumens 
ADD COLUMN IF NOT EXISTS sent_to_pembayaran_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS status_pembayaran ENUM('siap_dibayar', 'sudah_dibayar') NULL;

-- 2. Tandai migration 2025_11_23_232000 sudah dijalankan (jika belum ada)
INSERT IGNORE INTO migrations (migration, batch) 
VALUES ('2025_11_23_232000_add_pembayaran_fields_to_dokumens_table', 
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS m));

-- 3. Rollback migration yang gagal (jika ada di tabel migrations)
DELETE FROM migrations WHERE migration = '2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table';

-- Keluar dari MySQL
exit;
```

Kemudian jalankan migration lagi:
```bash
php artisan migrate --force
```

---

### **Opsi 2: Menggunakan Tinker (Lebih Aman)**

Jalankan di server:

```bash
# 1. Rollback migration yang gagal
php artisan migrate:rollback --step=1

# 2. Tambahkan kolom yang hilang menggunakan tinker
php artisan tinker
```

Di dalam tinker, jalankan:

```php
// Cek apakah kolom sudah ada
Schema::hasColumn('dokumens', 'status_pembayaran');
Schema::hasColumn('dokumens', 'sent_to_pembayaran_at');

// Jika belum ada, tambahkan
if (!Schema::hasColumn('dokumens', 'sent_to_pembayaran_at')) {
    DB::statement('ALTER TABLE dokumens ADD COLUMN sent_to_pembayaran_at TIMESTAMP NULL');
}

if (!Schema::hasColumn('dokumens', 'status_pembayaran')) {
    DB::statement("ALTER TABLE dokumens ADD COLUMN status_pembayaran ENUM('siap_dibayar', 'sudah_dibayar') NULL");
}

// Tandai migration 2025_11_23_232000 sudah dijalankan
if (!DB::table('migrations')->where('migration', '2025_11_23_232000_add_pembayaran_fields_to_dokumens_table')->exists()) {
    $batch = DB::table('migrations')->max('batch') + 1;
    DB::table('migrations')->insert([
        'migration' => '2025_11_23_232000_add_pembayaran_fields_to_dokumens_table',
        'batch' => $batch
    ]);
}

exit
```

Kemudian jalankan migration lagi:
```bash
php artisan migrate --force
```

---

### **Opsi 3: Edit File Migration Langsung di Server**

Jika file migration di server belum ter-update:

```bash
# 1. Pastikan file sudah ter-pull dengan benar
cd /var/www/agenda_online_ptpn
git pull origin main

# 2. Cek isi file migration
cat database/migrations/2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table.php | grep -A 10 "public function up"

# 3. Jika masih versi lama, edit file
nano database/migrations/2025_11_23_232538_add_link_bukti_pembayaran_to_dokumens_table.php
```

Pastikan method `up()` seperti ini:

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

Simpan (Ctrl+O, Enter, Ctrl+X), lalu:

```bash
# Rollback migration yang gagal
php artisan migrate:rollback --step=1

# Jalankan migration lagi
php artisan migrate --force
```

---

## Verifikasi Setelah Fix

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

---

## Catatan

- **Opsi 1 (SQL)** adalah yang tercepat jika Anda familiar dengan MySQL
- **Opsi 2 (Tinker)** lebih aman karena menggunakan Laravel
- **Opsi 3** diperlukan jika file migration di server belum ter-update

Setelah fix, migration seharusnya berjalan dengan lancar!

