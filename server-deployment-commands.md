# 🚀 Server Deployment Commands - VPS Alibaba

## 📋 **Step-by-Step Deployment Instructions**

### **1. SSH ke Server Anda**
```bash
ssh username@your-server-ip
```

### **2. Navigate ke Project Directory**
```bash
cd /var/www/agendaPTPN
```

### **3. Backup Aplikasi dan Database (PENTING!)**
```bash
# Buat backup directory jika belum ada
sudo mkdir -p /var/backups/agendaPTPN

# Backup application
sudo cp -r /var/www/agendaPTPN /var/backups/agendaPTPN/backup_$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u your_db_user -p your_database_name > /var/backups/agendaPTPN/db_backup_$(date +%Y%m%d_%H%M%S).sql
```

### **4. Download Deployment Script**
```bash
# Download script yang sudah saya buat
curl -o deploy.sh https://raw.githubusercontent.com/avsmenma/agendaPTPN/main/deploy.sh

# Make script executable
chmod +x deploy.sh
```

### **5. Jalankan Deployment Script**
```bash
# Untuk deployment tanpa seeders (aman untuk production)
./deploy.sh main

# Untuk deployment dengan seeders (HANYA jika dibutuhkan!)
# ./deploy.sh main --seed
```

### **6. Manual Deployment (Jika script tidak berfungsi)**
```bash
# Step 1: Pull latest changes
git stash push -m "Pre-deployment stash"
git fetch origin
git pull origin main

# Step 2: Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm install --production

# Step 3: Run migrations
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan migrate --force

# Step 4: Build frontend
npm run build

# Step 5: Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 6: Set permissions
sudo chown -R www-data:www-data /var/www/agendaPTPN
sudo chmod -R 755 /var/www/agendaPTPN
sudo chmod -R 777 /var/www/agendaPTPN/storage
sudo chmod -R 777 /var/www/agendaPTPN/bootstrap/cache

# Step 7: Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

### **7. Verifikasi Deployment**
```bash
# Check Laravel health
curl http://your-domain.com/up

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check Laravel logs
tail -f /var/www/agendaPTPN/storage/logs/laravel.log
```

---

## 🧪 **Post-Deployment Testing Checklist**

### **Critical Features to Test:**
- [ ] Homepage loads correctly
- [ ] Login functionality
- [ ] Dashboard displays data
- [ ] New deadline UI/UX styles (warna: hijau>=1hari, kuning<1hari, merah=terlambat)
- [ ] Document creation/editing
- [ ] File uploads/downloads
- [ ] Search functionality
- [ ] Export to Excel
- [ ] Real-time notifications

### **Test URLs:**
- Homepage: `http://your-domain.com`
- Login: `http://your-domain.com/login`
- Dashboard: `http://your-domain.com/dashboard`
- Documents: `http://your-domain.com/dokumens`
- Health Check: `http://your-domain.com/up`

---

## 🚨 **Troubleshooting Common Issues**

### **1. Composer Install Issues**
```bash
# Increase memory limit
php -d memory_limit=512M /usr/local/bin/composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
```

### **2. Permission Issues**
```bash
# Fix storage permissions
sudo chmod -R 777 /var/www/agendaPTPN/storage
sudo chmod -R 777 /var/www/agendaPTPN/bootstrap/cache
sudo chown -R www-data:www-data /var/www/agendaPTPN
```

### **3. Migration Issues**
```bash
# Check migration status
php artisan migrate:status

# Force rollback if needed (PENTING: Backup dulu!)
php artisan migrate:rollback --step=1
```

### **4. Nginx Configuration**
```bash
# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### **5. PHP-FPM Issues**
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 🔄 **Rollback Plan (If Something Goes Wrong)**

### **Quick Rollback:**
```bash
# Stop services
sudo systemctl stop nginx php8.2-fpm

# Restore from backup
sudo rm -rf /var/www/agendaPTPN
sudo cp -r /var/backups/agendaPTPN/backup_[timestamp]/app /var/www/agendaPTPN

# Restore database
mysql -u your_db_user -p your_database_name < /var/backups/agendaPTPN/db_backup_[timestamp].sql

# Start services
sudo systemctl start nginx php8.2-fpm
```

### **Git Rollback:**
```bash
# Check previous commits
git log --oneline -10

# Rollback to previous commit
git checkout previous-commit-hash

# If needed, create a new branch from that commit
git checkout -b rollback-branch previous-commit-hash
```

---

## 📊 **Monitoring After Deployment**

### **Check System Resources:**
```bash
# Disk usage
df -h

# Memory usage
free -h

# CPU usage
htop

# Check running processes
ps aux | grep php
```

### **Application Logs:**
```bash
# Laravel logs
tail -f /var/www/agendaPTPN/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

---

## ⚙️ **Server Requirements Check**

### **Verify Requirements:**
```bash
# Check PHP version
php -v
# Should be PHP 8.2+

# Check Node.js version
node -v
# Should be Node.js 18+

# Check Composer
composer --version

# Check Nginx
nginx -v

# Check MySQL/MariaDB
mysql --version
```

### **Install Missing Dependencies:**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-dom

# CentOS/RHEL
sudo yum install php82-php-fpm php82-php-mysqlnd php82-php-mbstring php82-php-xml php82-php-curl php82-php-zip
```

---

## 🎯 **Success Indicators**

### **Deployment Success When:**
- ✅ All services are running
- ✅ Homepage loads without errors
- ✅ Database migrations completed
- ✅ New UI/UX features are working
- ✅ No PHP/Nginx errors in logs
- ✅ File permissions are correct
- ✅ SSL certificate is valid

### **If Issues Occur:**
1. Check logs for specific errors
2. Verify all requirements are met
3. Try manual deployment steps
4. Rollback if critical issues
5. Contact support with error details

---

## 📞 **Support Information**

### **Useful Commands:**
```bash
# Laravel artisan commands
php artisan --help
php artisan route:list
php artisan migrate:status
php artisan config:cache

# System commands
systemctl status nginx
systemctl status php8.2-fpm
netstat -tlnp | grep :80
```

### **Important Files to Check:**
- `.env` - Environment configuration
- `storage/logs/laravel.log` - Laravel application logs
- `/var/log/nginx/error.log` - Nginx error logs
- `/etc/nginx/sites-available/your-domain` - Nginx configuration