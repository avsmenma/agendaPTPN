# ✅ Checklist Update Server - Pastikan Semua File Ter-push

## 🔍 Verifikasi di Local (SUDAH DILAKUKAN)

✅ **Commit terakhir:** `bfb238c improve rekap dokumen owner, add logs activity`
✅ **File penting sudah ter-commit:**
- `resources/views/owner/workflow.blade.php` - Modal popup untuk logs
- `app/Http/Controllers/DashboardBController.php` - Logging Ibu Yuni
- `app/Http/Controllers/DashboardPerpajakanController.php` - Logging Perpajakan
- `app/Http/Controllers/DashboardAkutansiController.php` - Logging Akutansi
- `app/Http/Controllers/DashboardPembayaranController.php` - Logging Pembayaran
- `app/Helpers/ActivityLogHelper.php` - Helper logging
- `app/Models/DokumenActivityLog.php` - Model activity log
- `database/migrations/2025_11_24_075859_create_dokumen_activity_logs_table.php` - Migration table logs
- `database/migrations/2025_11_24_082236_add_kebun_to_dokumens_table.php` - Migration field kebun

✅ **Git status:** Clean, semua file sudah ter-commit dan ter-push

---

## 🚀 Langkah-langkah Update di Server

### **1. SSH ke Server**
```bash
ssh username@your-server-ip
# atau
ssh -i /path/to/key.pem username@your-server-ip
```

### **2. Masuk ke Direktori Project**
```bash
cd /var/www/agenda_online_ptpn
# atau path sesuai lokasi project Anda
```

### **3. Cek Status Git di Server**
```bash
# Cek apakah ada perubahan lokal yang belum di-commit
git status

# Cek commit terakhir
git log --oneline -5

# Cek apakah branch sudah up-to-date dengan remote
git fetch origin
git status
```

### **4. Pull Perubahan Terbaru**
```bash
# Pull perubahan dari GitHub
git pull origin main

# Jika ada konflik, resolve dulu
# Jika tidak ada konflik, lanjut ke langkah berikutnya
```

### **5. Verifikasi File Penting Sudah Ter-pull**
```bash
# Cek apakah file workflow.blade.php sudah ter-update
grep -n "openActivityLogsModal" resources/views/owner/workflow.blade.php

# Cek apakah ActivityLogHelper.php ada
ls -la app/Helpers/ActivityLogHelper.php

# Cek apakah migration sudah ada
ls -la database/migrations/2025_11_24_*.php
```

### **6. Run Migrations (PENTING!)**
```bash
# Backup database dulu (WAJIB!)
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migrations
php artisan migrate --force

# Cek status migration
php artisan migrate:status
```

### **7. Clear SEMUA Cache (SANGAT PENTING!)**
```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache (PENTING untuk Blade files!)
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **8. Install/Update Dependencies (jika diperlukan)**
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install/Update frontend dependencies
npm install

# Build frontend assets
npm run build
```

### **9. Set Permissions**
```bash
# Set permissions untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **10. Restart Services**
```bash
# Restart PHP-FPM (sesuaikan versi PHP)
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

### **11. Clear Browser Cache**
- **Chrome/Edge:** Ctrl+Shift+R (Windows) atau Cmd+Shift+R (Mac)
- **Firefox:** Ctrl+F5 (Windows) atau Cmd+Shift+R (Mac)
- **Atau:** Buka Developer Tools (F12) > Right click pada tombol refresh > "Empty Cache and Hard Reload"

---

## 🔧 Troubleshooting

### **Masalah 1: Website Masih Menampilkan Versi Lama**

**Kemungkinan penyebab:**
1. View cache belum di-clear
2. Browser cache masih menyimpan versi lama
3. PHP-FPM belum di-restart
4. File belum ter-pull dengan benar

**Solusi:**
```bash
# 1. Pastikan git pull berhasil
git pull origin main

# 2. Clear view cache (PENTING!)
php artisan view:clear

# 3. Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 4. Rebuild cache
php artisan config:cache
php artisan view:cache
php artisan route:cache

# 5. Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# 6. Restart web server
sudo systemctl restart nginx

# 7. Clear browser cache (Ctrl+Shift+R)
```

### **Masalah 2: Modal Popup Tidak Muncul**

**Kemungkinan penyebab:**
1. File `workflow.blade.php` belum ter-update
2. JavaScript error di browser console
3. View cache masih menyimpan versi lama

**Solusi:**
```bash
# 1. Verifikasi file sudah ter-update
grep -n "activityLogsModal" resources/views/owner/workflow.blade.php

# 2. Clear view cache
php artisan view:clear

# 3. Rebuild view cache
php artisan view:cache

# 4. Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# 5. Cek browser console untuk error JavaScript (F12)
```

### **Masalah 3: Activity Logs Tidak Muncul**

**Kemungkinan penyebab:**
1. Migration belum dijalankan
2. Table `dokumen_activity_logs` belum ada
3. Logging belum di-trigger

**Solusi:**
```bash
# 1. Cek apakah migration sudah dijalankan
php artisan migrate:status

# 2. Jalankan migration jika belum
php artisan migrate --force

# 3. Cek apakah table sudah ada
php artisan tinker
>>> Schema::hasTable('dokumen_activity_logs')
>>> exit

# 4. Cek log Laravel untuk error
tail -f storage/logs/laravel.log
```

### **Masalah 4: Field Kebun Tidak Muncul**

**Kemungkinan penyebab:**
1. Migration `add_kebun_to_dokumens_table` belum dijalankan
2. Kolom `kebun` belum ada di database

**Solusi:**
```bash
# 1. Jalankan migration
php artisan migrate --force

# 2. Cek apakah kolom sudah ada
php artisan tinker
>>> Schema::hasColumn('dokumens', 'kebun')
>>> exit
```

---

## 📋 Checklist Final

Sebelum menutup SSH session, pastikan:

- [ ] Git pull berhasil tanpa error
- [ ] Migration berhasil dijalankan
- [ ] Semua cache sudah di-clear
- [ ] View cache sudah di-rebuild
- [ ] PHP-FPM sudah di-restart
- [ ] Web server sudah di-restart
- [ ] File penting sudah ter-verifikasi (workflow.blade.php, ActivityLogHelper.php)
- [ ] Browser cache sudah di-clear
- [ ] Website sudah di-test dan berfungsi dengan baik

---

## 🎯 Quick Command (Copy-Paste Semua Sekaligus)

```bash
# Masuk ke direktori project
cd /var/www/agenda_online_ptpn

# Pull perubahan
git pull origin main

# Backup database (WAJIB!)
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migrations
php artisan migrate --force

# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install dependencies (jika diperlukan)
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Restart services
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

---

## 📞 Jika Masih Ada Masalah

1. **Cek log Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Cek log web server:**
   ```bash
   # Nginx
   tail -f /var/log/nginx/error.log
   
   # Apache
   tail -f /var/log/apache2/error.log
   ```

3. **Cek status services:**
   ```bash
   sudo systemctl status php8.4-fpm
   sudo systemctl status nginx
   ```

4. **Verifikasi file sudah ter-pull:**
   ```bash
   git log --oneline -5
   git show HEAD:resources/views/owner/workflow.blade.php | grep -n "activityLogsModal"
   ```

