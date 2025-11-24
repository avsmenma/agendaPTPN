# 📋 Deployment Checklist - Agenda PTPN Laravel Application

## 🚀 **Persiapan Sebelum Deployment**

### **✅ Local Preparation**
- [ ] Git status clean (no uncommitted changes)
- [ ] All tests passing (`php artisan test`)
- [ ] Code optimized (`php artisan config:cache`, `route:cache`)
- [ ] Frontend assets built (`npm run build`)
- [ ] Database migrations tested locally
- [ ] `.env.example` updated if needed

### **✅ Server Requirements Check**
- [ ] PHP 8.2+ installed
- [ ] Composer installed and updated
- [ ] Node.js 18+ and npm installed
- [ ] MySQL/MariaDB database ready
- [ ] Nginx/Apache configured
- [ ] SSL certificate installed
- [ ] Sufficient disk space (> 2GB free)

### **✅ Database Preparation**
- [ ] Current database backup created
- [ ] Check migration versions between local and server
- [ ] Prepare rollback script
- [ ] Test migrations on staging database first

---

## 🔄 **Proses Deployment**

### **1. Pre-Deployment**
```bash
# SSH ke server VPS Alibaba
ssh user@your-server-ip

# Navigate to project directory
cd /var/www/agendaPTPN

# Create backup
./backup.sh
```

### **2. Update Application**
```bash
# Stash any server changes
git stash push -m "Pre-deployment stash"

# Pull latest changes
git fetch origin
git pull origin main

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm install --production
```

### **3. Database Migration**
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Run seeders if needed (WARNING: Only for first-time or specific updates)
# php artisan db:seed --class=BidangSeeder --force
# php artisan db:seed --class=UserSeeder --force
```

### **4. Application Optimization**
```bash
# Build frontend
npm run build

# Cache Laravel components
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data /var/www/agendaPTPN
chmod -R 755 /var/www/agendaPTPN
chmod -R 777 /var/www/agendaPTPN/storage
chmod -R 777 /var/www/agendaPTPN/bootstrap/cache
```

### **5. Service Restart**
```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart Nginx
sudo systemctl restart nginx

# Restart Supervisor (if using queues)
sudo supervisorctl restart all
```

---

## 🧪 **Post-Deployment Testing**

### **✅ Functionality Tests**
- [ ] Homepage loads correctly
- [ ] Login functionality works
- [ ] Dashboard displays data
- [ ] Create/Edit/Delete document functions
- [ ] File upload/download works
- [ ] Search functionality works
- [ ] Export to Excel works
- [ ] Real-time notifications work

### **✅ Performance Tests**
- [ ] Page load times acceptable (< 3 seconds)
- [ ] Database queries optimized
- [ ] Assets loading correctly
- [ ] No JavaScript errors in console
- [ ] Mobile responsiveness works

### **✅ Security Checks**
- [ ] HTTPS working properly
- [ ] No sensitive information exposed
- [ ] File permissions correct
- [ ] Error pages customized
- [ ] Authentication working

---

## 🚨 **Rollback Plan**

### **If Critical Issues Occur:**
```bash
# Quick rollback to previous commit
git checkout previous-commit-hash

# Or restore from backup
sudo systemctl stop nginx php8.2-fpm
sudo rm -rf /var/www/agendaPTPN
sudo cp -r /var/backups/agendaPTPN/latest-backup /var/www/agendaPTPN
sudo systemctl start nginx php8.2-fpm

# Restore database
mysql -u username -p database_name < backup.sql
```

---

## 📊 **Monitoring After Deployment**

### **Check Application Health:**
```bash
# Laravel health check
curl http://your-domain.com/up

# Check logs
tail -f /var/log/nginx/error.log
tail -f /var/www/agendaPTPN/storage/logs/laravel.log

# Check system resources
htop
df -h
free -h
```

### **Monitor Specific Features:**
- [ ] New deadline UI/UX styles working
- [ ] Document workflow functioning
- [ ] User permissions correct
- [ ] Email notifications working
- [ ] File uploads functioning

---

## ⚠️ **Important Notes**

### **Database Migration Warnings:**
- Always backup database before migrations
- Test migrations on staging first
- Some migrations may require manual data migration
- Check for data loss in critical columns

### **Performance Considerations:**
- Clear caches after deployment
- Monitor memory usage
- Check queue workers if using them
- Optimize large tables if needed

### **Security Considerations:**
- Update `.env` file permissions (600)
- Check file upload permissions
- Verify SSL certificate validity
- Monitor for unusual activity

---

## 🛠️ **Useful Commands**

### **Laravel Commands:**
```bash
# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# Check migration status
php artisan migrate:status

# View queue status
php artisan queue:failed
```

### **System Commands:**
```bash
# Check disk usage
df -h

# Check memory usage
free -h

# Check running processes
ps aux | grep php

# Check Nginx status
systemctl status nginx
```

---

## 📞 **Emergency Contacts**

### **If Deployment Fails:**
1. Check logs for errors
2. Restore from backup if critical
3. Contact development team
4. Document the issue for future reference

### **Success Criteria:**
- All tests passing
- No performance degradation
- All features working as expected
- No security vulnerabilities
- Users can access and use the system