# 🔧 Fix: Tampilan Lama di Server Setelah Git Pull

## 🎯 Masalah
- ✅ Git pull sudah dilakukan di server
- ❌ Tampilan masih lama (tidak ada link "Klik untuk melihat aktivitas")
- ✅ Di localhost sudah muncul fitur modal popup

## 🔍 Penyebab
**View cache Laravel belum di-clear!** Laravel meng-cache compiled Blade files di `storage/framework/views/`. Tanpa clear cache ini, perubahan di `.blade.php` tidak akan terlihat.

## ✅ Solusi Lengkap (Jalankan di Server)

### **Langkah 1: Verifikasi File Sudah Ter-pull**

```bash
# Masuk ke direktori project
cd /var/www/agenda_online_ptpn

# Cek commit terakhir
git log --oneline -3

# Verifikasi file workflow.blade.php sudah ter-update
grep -n "activityLogsModal" resources/views/owner/workflow.blade.php

# Jika muncul hasil, berarti file sudah ter-pull
# Jika tidak muncul, berarti file belum ter-pull dengan benar
```

### **Langkah 2: Clear SEMUA Cache (SANGAT PENTING!)**

```bash
# Clear semua cache Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear  # ← INI YANG PALING PENTING!

# Hapus manual compiled views (jika perlu)
rm -rf storage/framework/views/*.php

# Clear compiled classes
php artisan clear-compiled

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Langkah 3: Restart PHP-FPM (WAJIB!)**

```bash
# Restart PHP-FPM (sesuaikan versi PHP Anda)
sudo systemctl restart php8.4-fpm
# atau
sudo systemctl restart php8.2-fpm
# atau
sudo systemctl restart php8.1-fpm

# Cek status PHP-FPM
sudo systemctl status php8.4-fpm
```

### **Langkah 4: Restart Web Server**

```bash
# Restart Nginx
sudo systemctl restart nginx

# Atau jika menggunakan Apache
sudo systemctl restart apache2

# Cek status
sudo systemctl status nginx
```

### **Langkah 5: Verifikasi File dan Cache**

```bash
# Cek apakah file sudah ter-update
grep -n "Klik untuk melihat aktivitas" resources/views/owner/workflow.blade.php

# Cek apakah compiled view sudah terhapus
ls -la storage/framework/views/ | head -20

# Cek log untuk error
tail -20 storage/logs/laravel.log
```

### **Langkah 6: Clear Browser Cache**

Di browser:
- **Chrome/Edge:** `Ctrl+Shift+R` (Windows) atau `Cmd+Shift+R` (Mac)
- **Firefox:** `Ctrl+F5` (Windows) atau `Cmd+Shift+R` (Mac)
- **Atau:** F12 > Right click tombol refresh > "Empty Cache and Hard Reload"

---

## 🚀 Quick Fix Command (Copy-Paste Semua Sekaligus)

```bash
cd /var/www/agenda_online_ptpn

# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Hapus compiled views manual
rm -rf storage/framework/views/*.php

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.4-fpm  # sesuaikan versi
sudo systemctl restart nginx

# Verifikasi
grep -n "Klik untuk melihat aktivitas" resources/views/owner/workflow.blade.php
echo "✅ Jika muncul hasil, file sudah ter-update"
```

---

## 🔍 Troubleshooting Lanjutan

### **Jika Masih Tidak Muncul:**

#### **1. Cek Apakah File Benar-benar Ter-pull**

```bash
# Cek commit terakhir di server
git log --oneline -5

# Bandingkan dengan commit di GitHub
# Pastikan commit hash sama

# Jika berbeda, pull lagi
git fetch origin
git pull origin main
```

#### **2. Cek Permission File**

```bash
# Pastikan file bisa dibaca
ls -la resources/views/owner/workflow.blade.php

# Jika permission salah, fix
chmod 644 resources/views/owner/workflow.blade.php
chown www-data:www-data resources/views/owner/workflow.blade.php
```

#### **3. Cek Apakah Ada Error di Log**

```bash
# Cek log Laravel
tail -50 storage/logs/laravel.log

# Cek log PHP-FPM
sudo tail -50 /var/log/php8.4-fpm.log  # sesuaikan versi

# Cek log Nginx
sudo tail -50 /var/log/nginx/error.log
```

#### **4. Force Recompile Views**

```bash
# Hapus semua compiled views
find storage/framework/views -name "*.php" -type f -delete

# Clear cache lagi
php artisan view:clear

# Rebuild
php artisan view:cache

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

#### **5. Cek Apakah Ada Opcache (PHP Opcache)**

```bash
# Clear opcache jika ada
php artisan opcache:clear  # jika ada command ini

# Atau restart PHP-FPM (sudah termasuk clear opcache)
sudo systemctl restart php8.4-fpm
```

#### **6. Hard Refresh di Browser**

- Buka Developer Tools (F12)
- Go to Network tab
- Check "Disable cache"
- Refresh halaman (F5)
- Atau gunakan Incognito/Private window

---

## 📋 Checklist Verifikasi

Setelah menjalankan semua command, pastikan:

- [ ] `git log --oneline -3` menunjukkan commit terbaru
- [ ] `grep -n "activityLogsModal" resources/views/owner/workflow.blade.php` mengembalikan hasil
- [ ] `grep -n "Klik untuk melihat aktivitas" resources/views/owner/workflow.blade.php` mengembalikan hasil
- [ ] `storage/framework/views/` sudah di-clear (tidak ada file lama)
- [ ] PHP-FPM sudah di-restart
- [ ] Nginx sudah di-restart
- [ ] Browser cache sudah di-clear
- [ ] Website sudah di-test dan link "Klik untuk melihat aktivitas" muncul

---

## 🎯 Command Paling Penting (Jika Hanya Bisa Jalankan Satu)

```bash
cd /var/www/agenda_online_ptpn && php artisan view:clear && rm -rf storage/framework/views/*.php && sudo systemctl restart php8.4-fpm && sudo systemctl restart nginx
```

Command ini akan:
1. Clear view cache
2. Hapus semua compiled views
3. Restart PHP-FPM
4. Restart Nginx

**Setelah itu, clear browser cache (Ctrl+Shift+R) dan refresh halaman.**

---

## 💡 Penjelasan

**Mengapa view cache sangat penting?**

Laravel meng-compile file `.blade.php` menjadi PHP biasa dan menyimpannya di `storage/framework/views/`. File compiled ini di-cache untuk performa. Ketika Anda mengubah file `.blade.php`, Laravel tidak otomatis meng-compile ulang - Anda harus clear cache dulu.

**Mengapa perlu restart PHP-FPM?**

PHP-FPM juga meng-cache opcode (compiled PHP). Restart PHP-FPM akan clear opcache dan memastikan PHP menggunakan file terbaru.

---

## 📞 Jika Masih Tidak Berhasil

1. **Cek apakah file benar-benar ter-pull:**
   ```bash
   git show HEAD:resources/views/owner/workflow.blade.php | grep -n "activityLogsModal"
   ```

2. **Cek apakah ada konflik atau merge issue:**
   ```bash
   git status
   git diff HEAD resources/views/owner/workflow.blade.php
   ```

3. **Cek apakah ada file .env yang berbeda:**
   ```bash
   # Pastikan APP_ENV=production atau sesuai
   grep APP_ENV .env
   ```

4. **Coba pull ulang dengan force:**
   ```bash
   git fetch origin
   git reset --hard origin/main
   php artisan view:clear
   sudo systemctl restart php8.4-fpm
   ```

