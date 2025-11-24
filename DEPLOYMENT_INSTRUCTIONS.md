# Instruksi Deployment ke Server

## Langkah-langkah Update Website di Server

### 1. SSH ke Server
```bash
ssh username@your-server-ip
# atau
ssh -i /path/to/your-key.pem username@your-server-ip
```

### 2. Masuk ke Direktori Project
```bash
cd /path/to/agenda_online_ptpn
# Contoh: cd /var/www/agenda_online_ptpn
```

### 3. Pull Perubahan Terbaru dari GitHub
```bash
git pull origin main
```

### 4. Install Dependencies (jika ada perubahan)
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install/Update frontend dependencies (jika ada perubahan)
npm install

# Build frontend assets
npm run build
```

### 5. Run Migrations (jika ada migration baru)
```bash
php artisan migrate --force
```

### 6. Clear Cache
```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Optimize (opsional, untuk production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set Permissions (jika diperlukan)
```bash
# Set permissions untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Restart PHP-FPM (jika diperlukan)
```bash
# Ubuntu/Debian
sudo systemctl restart php8.4-fpm
# atau
sudo service php8.4-fpm restart

# CentOS/RHEL
sudo systemctl restart php-fpm
```

### 9. Restart Web Server (jika diperlukan)
```bash
# Nginx
sudo systemctl restart nginx

# Apache
sudo systemctl restart apache2
```

## Perubahan Terbaru yang Di-deploy

### Fitur Baru:
1. **Comprehensive Activity Logging**
   - Logging untuk semua aksi (set deadline, kirim dokumen, edit data) di semua modul
   - Logging untuk Ibu Yuni: set deadline, kirim ke perpajakan/akutansi
   - Logging untuk Team Perpajakan: set deadline, kirim ke akutansi, edit data
   - Logging untuk Team Akutansi: set deadline, kirim ke pembayaran, edit data
   - Logging untuk Team Pembayaran: update status, upload bukti

2. **Modal Popup untuk Activity Logs**
   - Logs aktivitas ditampilkan dalam modal popup untuk menjaga UI tetap rapi
   - Modal dengan ukuran fix, bisa di-scroll, dan UI/UX yang ramah

3. **Field Kebun**
   - Field kebun ditambahkan ke semua form (Ibu Tarapul, Ibu Yuni, Team Perpajakan, Team Akutansi)
   - Field kebun ditampilkan di semua detail dokumen

### File yang Diubah:
- `resources/views/owner/workflow.blade.php` - Modal popup untuk logs
- `app/Http/Controllers/DashboardBController.php` - Logging untuk Ibu Yuni
- `app/Http/Controllers/DashboardPerpajakanController.php` - Logging untuk Perpajakan
- `app/Http/Controllers/DashboardAkutansiController.php` - Logging untuk Akutansi
- `app/Http/Controllers/DashboardPembayaranController.php` - Logging untuk Pembayaran
- `app/Helpers/ActivityLogHelper.php` - Helper untuk logging
- Semua view detail dokumen - Menambahkan field kebun

## Troubleshooting

### Jika ada error setelah pull:
1. **Error: Migration**
   ```bash
   php artisan migrate:status
   php artisan migrate --force
   ```

2. **Error: Cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Error: Permission**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

4. **Error: Composer**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Error: NPM**
   ```bash
   npm install
   npm run build
   ```

## Catatan Penting

- Pastikan backup database dilakukan sebelum migration
- Pastikan semua environment variables sudah di-set dengan benar
- Pastikan PHP version sesuai dengan requirement (PHP 8.4)
- Pastikan semua extension PHP yang diperlukan sudah terinstall

