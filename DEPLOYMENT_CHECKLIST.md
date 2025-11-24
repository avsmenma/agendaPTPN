# ✅ Deployment Checklist

Gunakan checklist ini untuk memastikan semua langkah deployment dilakukan dengan benar.

## 📍 Lokal (Development Machine)

### Sebelum Push ke GitHub
- [ ] Semua perubahan sudah di-test di local
- [ ] Tidak ada error atau warning yang signifikan
- [ ] Semua file penting sudah di-commit
- [ ] File `.env` TIDAK di-commit (sudah di .gitignore)
- [ ] Database migration files sudah ada dan benar

### Git Commands
```bash
# Cek status
git status

# Tambahkan perubahan
git add .

# Commit dengan pesan yang jelas
git commit -m "Deskripsi perubahan"

# Push ke GitHub
git push origin main
```

---

## 🖥️ Server (VPS Ubuntu Alibaba)

### Persiapan
- [ ] SSH ke server berhasil
- [ ] Sudah masuk ke direktori project
- [ ] Backup database sudah dibuat
- [ ] File `.env` sudah di-backup

### Deployment Steps
- [ ] `git pull origin main` - Pull perubahan terbaru
- [ ] `composer install --no-dev --optimize-autoloader` - Update dependencies
- [ ] `npm install` - Update NPM packages
- [ ] `npm run build` - Build assets
- [ ] `php artisan migrate --force` - Run migrations
- [ ] `php artisan config:clear` - Clear config cache
- [ ] `php artisan cache:clear` - Clear application cache
- [ ] `php artisan route:clear` - Clear route cache
- [ ] `php artisan view:clear` - Clear view cache
- [ ] `php artisan config:cache` - Cache config
- [ ] `php artisan route:cache` - Cache routes
- [ ] `php artisan view:cache` - Cache views
- [ ] Set permissions untuk storage dan bootstrap/cache
- [ ] Restart PHP-FPM (jika perlu)

### Verifikasi
- [ ] Website bisa diakses
- [ ] Tampilan sudah update
- [ ] Fitur baru sudah berfungsi
- [ ] Tidak ada error di halaman
- [ ] Database sudah ter-update dengan benar

---

## 🔍 Post-Deployment Checks

### Cek Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs (jika menggunakan Nginx)
tail -f /var/log/nginx/error.log

# Apache logs (jika menggunakan Apache)
tail -f /var/log/apache2/error.log
```

### Cek Services
```bash
# Status Nginx
sudo systemctl status nginx

# Status Apache
sudo systemctl status apache2

# Status PHP-FPM
sudo systemctl status php8.2-fpm
```

### Cek Permissions
```bash
# Pastikan storage bisa ditulis
ls -la storage/
ls -la bootstrap/cache/
```

---

## ⚠️ Rollback Plan (Jika Ada Masalah)

Jika deployment gagal atau ada masalah:

1. **Rollback Git:**
   ```bash
   git log  # Lihat commit sebelumnya
   git reset --hard HEAD~1  # Kembali ke commit sebelumnya
   ```

2. **Rollback Database:**
   ```bash
   php artisan migrate:rollback
   ```

3. **Restore .env:**
   ```bash
   cp .env.backup.YYYYMMDD_HHMMSS .env
   ```

4. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## 📝 Notes

- Simpan backup database sebelum migration
- Jangan lupa backup `.env` file
- Test di staging environment dulu (jika ada)
- Monitor logs setelah deployment
- Informasikan tim jika ada breaking changes
