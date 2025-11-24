# 🚀 Panduan Git Pull di Server - Update Website

## 📋 Situasi Saat Ini

- ✅ Branch lokal tertinggal **5 commits** dari `origin/main`
- ⚠️ Ada perubahan lokal pada file `.gitignore` (bisa diabaikan)
- ⚠️ Ada file untracked (tidak perlu di-commit)

## 🔧 Langkah-langkah Update

### **1. Discard Perubahan Lokal pada .gitignore (Aman untuk Diabaikan)**

File `.gitignore` yang modified adalah file konfigurasi lokal yang tidak perlu di-commit. Kita bisa discard perubahan ini:

```bash
# Discard perubahan pada .gitignore files
git restore bootstrap/cache/.gitignore
git restore storage/app/.gitignore
git restore storage/app/private/.gitignore
git restore storage/app/public/.gitignore
git restore storage/framework/.gitignore
git restore storage/framework/cache/.gitignore
git restore storage/framework/cache/data/.gitignore
git restore storage/framework/sessions/.gitignore
git restore storage/framework/testing/.gitignore
git restore storage/framework/views/.gitignore
git restore storage/logs/.gitignore

# Untuk deploy.sh, cek dulu apakah ada perubahan penting
# Jika tidak ada perubahan penting, discard juga:
git restore deploy.sh
```

**Atau lebih cepat, discard semua perubahan sekaligus:**
```bash
git restore .
```

### **2. Pull Perubahan dari GitHub**

```bash
# Pull perubahan terbaru
git pull origin main
```

### **3. Backup Database (WAJIB!)**

```bash
# Backup database sebelum migration
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Ganti your_username dan your_database_name dengan nilai yang sesuai
# Contoh:
# mysqldump -u root -p agenda_online > backup_$(date +%Y%m%d_%H%M%S).sql
```

### **4. Run Migrations**

```bash
# Jalankan migrations
php artisan migrate --force

# Cek status migration
php artisan migrate:status
```

### **5. Clear SEMUA Cache (SANGAT PENTING!)**

```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear  # ← INI SANGAT PENTING untuk Blade files!

# Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **6. Install/Update Dependencies (jika diperlukan)**

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install/Update frontend dependencies
npm install

# Build frontend assets
npm run build
```

### **7. Set Permissions**

```bash
# Set permissions untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **8. Restart Services**

```bash
# Restart PHP-FPM (sesuaikan versi PHP Anda)
sudo systemctl restart php8.4-fpm
# atau
sudo systemctl restart php8.2-fpm
# atau
sudo systemctl restart php8.1-fpm

# Restart web server
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

### **9. Verifikasi File Penting Sudah Ter-update**

```bash
# Cek apakah modal popup sudah ada di workflow.blade.php
grep -n "activityLogsModal" resources/views/owner/workflow.blade.php

# Cek apakah ActivityLogHelper.php ada
ls -la app/Helpers/ActivityLogHelper.php

# Cek commit terakhir
git log --oneline -5
```

---

## 🎯 Quick Command (Copy-Paste Semua Sekaligus)

```bash
# 1. Discard perubahan lokal pada .gitignore
git restore .

# 2. Pull perubahan dari GitHub
git pull origin main

# 3. Backup database (WAJIB - ganti username dan database name!)
mysqldump -u root -p agenda_online > backup_$(date +%Y%m%d_%H%M%S).sql

# 4. Run migrations
php artisan migrate --force

# 5. Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Install dependencies (jika diperlukan)
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 8. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 9. Restart services
sudo systemctl restart php8.4-fpm  # atau php8.2-fpm sesuai versi
sudo systemctl restart nginx

# 10. Verifikasi
git log --oneline -5
grep -n "activityLogsModal" resources/views/owner/workflow.blade.php
```

---

## ⚠️ Catatan Penting

1. **File Untracked Tidak Perlu Di-commit:**
   - `database/seeders/DummyDataSeeder.php` - File seeder lokal
   - `public/build/` - File build yang di-generate
   - `public/test.php` - File test lokal
   
   File-file ini bisa diabaikan atau dihapus jika tidak diperlukan.

2. **Perubahan pada .gitignore:**
   - Perubahan ini biasanya terjadi karena perbedaan konfigurasi antara local dan server
   - Aman untuk di-discard karena file `.gitignore` sudah ada di repository

3. **Setelah Update:**
   - Clear browser cache: **Ctrl+Shift+R** (Windows) atau **Cmd+Shift+R** (Mac)
   - Atau: F12 > Right click tombol refresh > "Empty Cache and Hard Reload"

---

## 🔍 Troubleshooting

### Jika git pull error karena konflik:
```bash
# Abort merge jika ada konflik
git merge --abort

# Atau reset ke state sebelum pull
git reset --hard HEAD

# Lalu pull lagi
git pull origin main
```

### Jika migration error:
```bash
# Cek status migration
php artisan migrate:status

# Rollback 1 step jika perlu
php artisan migrate:rollback --step=1

# Lalu jalankan lagi
php artisan migrate --force
```

### Jika website masih menampilkan versi lama:
```bash
# Clear view cache lagi (PENTING!)
php artisan view:clear
php artisan view:cache

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Clear browser cache (Ctrl+Shift+R)
```

---

## ✅ Checklist Final

Setelah menjalankan semua command, pastikan:

- [ ] Git pull berhasil tanpa error
- [ ] Migration berhasil dijalankan
- [ ] Semua cache sudah di-clear
- [ ] View cache sudah di-rebuild
- [ ] PHP-FPM sudah di-restart
- [ ] Web server sudah di-restart
- [ ] File `workflow.blade.php` sudah ter-verifikasi (ada `activityLogsModal`)
- [ ] Browser cache sudah di-clear
- [ ] Website sudah di-test dan berfungsi dengan baik

