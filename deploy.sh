#!/bin/bash

# ============================================
# Script Deployment Otomatis untuk Laravel
# ============================================

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fungsi untuk print dengan warna
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Header
echo "=========================================="
echo "  Laravel Deployment Script"
echo "=========================================="
echo ""

# Cek apakah di direktori project Laravel
if [ ! -f "artisan" ]; then
    print_error "File artisan tidak ditemukan. Pastikan Anda berada di direktori root project Laravel."
    exit 1
fi

# 1. Backup .env
print_info "1. Membuat backup .env..."
if [ -f ".env" ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_success "Backup .env berhasil dibuat"
else
    print_warning "File .env tidak ditemukan"
fi

# 2. Git Pull
print_info "2. Pull perubahan dari GitHub..."
if git pull origin main; then
    print_success "Git pull berhasil"
else
    print_error "Git pull gagal. Pastikan koneksi internet dan repository benar."
    exit 1
fi

# 3. Install Composer Dependencies
print_info "3. Install/Update Composer dependencies..."
if composer install --no-dev --optimize-autoloader; then
    print_success "Composer dependencies berhasil diinstall"
else
    print_error "Composer install gagal"
    exit 1
fi

# 4. Install NPM Dependencies
print_info "4. Install/Update NPM dependencies..."
if npm install; then
    print_success "NPM dependencies berhasil diinstall"
else
    print_warning "NPM install gagal, tetapi melanjutkan..."
fi

# 5. Build Assets
print_info "5. Build assets dengan Vite..."
if npm run build; then
    print_success "Assets berhasil di-build"
else
    print_warning "Build assets gagal, tetapi melanjutkan..."
fi

# 6. Run Migrations
print_info "6. Menjalankan database migrations..."
read -p "Apakah Anda yakin ingin menjalankan migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if php artisan migrate --force; then
        print_success "Migrations berhasil dijalankan"
    else
        print_error "Migrations gagal. Cek log untuk detail."
        exit 1
    fi
else
    print_warning "Migrations dilewati"
fi

# 7. Clear Cache
print_info "7. Membersihkan cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Cache berhasil dibersihkan"

# 8. Optimize Application
print_info "8. Optimasi aplikasi..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Aplikasi berhasil dioptimasi"

# 9. Set Permissions
print_info "9. Mengatur permissions..."
chmod -R 755 storage bootstrap/cache
if [ -n "$SUDO_USER" ]; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || print_warning "Tidak bisa mengubah ownership (mungkin perlu sudo)"
else
    print_warning "Permissions tidak diubah (jalankan dengan sudo untuk mengubah ownership)"
fi
print_success "Permissions berhasil diatur"

# 10. Restart PHP-FPM (opsional)
print_info "10. Restart PHP-FPM..."
read -p "Apakah Anda ingin restart PHP-FPM? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if sudo systemctl restart php8.2-fpm 2>/dev/null || sudo service php-fpm restart 2>/dev/null; then
        print_success "PHP-FPM berhasil di-restart"
    else
        print_warning "Gagal restart PHP-FPM (mungkin tidak terinstall atau perlu konfigurasi manual)"
    fi
else
    print_warning "PHP-FPM tidak di-restart"
fi

# Selesai
echo ""
echo "=========================================="
print_success "Deployment selesai!"
echo "=========================================="
echo ""
print_info "Langkah selanjutnya:"
echo "  1. Cek website apakah sudah update"
echo "  2. Jika masih ada masalah, cek log: tail -f storage/logs/laravel.log"
echo "  3. Clear browser cache (Ctrl+Shift+R)"
echo ""
