#!/bin/bash

# Deployment Script for Laravel Agenda PTPN
# Usage: ./deploy.sh [branch]
# Default branch: main

set -e  # Exit on any error

echo "🚀 Starting deployment process..."

# Configuration
BRANCH=${1:-main}
APP_DIR="/var/www/agendaPTPN"
BACKUP_DIR="/var/backups/agendaPTPN"
NGINX_SERVICE="nginx"
PHP_SERVICE="php8.2-fpm"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if service exists
check_service() {
    if systemctl list-unit-files | grep -q "^$1.service"; then
        return 0
    else
        return 1
    fi
}

# Create backup directory if not exists
create_backup() {
    log_info "Creating backup..."

    if [ ! -d "$BACKUP_DIR" ]; then
        sudo mkdir -p "$BACKUP_DIR"
    fi

    BACKUP_NAME="backup_$(date +%Y%m%d_%H%M%S)"
    sudo mkdir -p "$BACKUP_DIR/$BACKUP_NAME"

    # Backup current application
    if [ -d "$APP_DIR" ]; then
        sudo cp -r "$APP_DIR" "$BACKUP_DIR/$BACKUP_NAME/app"
        log_info "Application backed up to $BACKUP_DIR/$BACKUP_NAME/app"
    fi

    # Backup database
    if command -v mysql &> /dev/null; then
        if [ -f "$APP_DIR/.env" ]; then
            DB_DATABASE=$(grep DB_DATABASE "$APP_DIR/.env" | cut -d '=' -f2)
            DB_USERNAME=$(grep DB_USERNAME "$APP_DIR/.env" | cut -d '=' -f2)
            DB_PASSWORD=$(grep DB_PASSWORD "$APP_DIR/.env" | cut -d '=' -f2)

            if [ ! -z "$DB_DATABASE" ]; then
                mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_DIR/$BACKUP_NAME/database.sql"
                log_info "Database backed up to $BACKUP_DIR/$BACKUP_NAME/database.sql"
            fi
        fi
    fi
}

# Update application code
update_code() {
    log_info "Updating application code..."

    if [ ! -d "$APP_DIR" ]; then
        log_error "Application directory $APP_DIR does not exist"
        exit 1
    fi

    cd "$APP_DIR"

    # Stash any local changes
    sudo -u www-data git stash push -m "Stashing changes before deployment $(date)"

    # Fetch latest changes
    sudo -u www-data git fetch origin

    # Checkout the specified branch
    sudo -u www-data git checkout "$BRANCH"

    # Pull latest changes
    sudo -u www-data git pull origin "$BRANCH"

    log_info "Code updated to latest $BRANCH branch"
}

# Install/update dependencies
install_dependencies() {
    log_info "Installing dependencies..."

    cd "$APP_DIR"

    # Install PHP dependencies
    sudo -u www-data composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

    # Install Node.js dependencies
    sudo -u www-data npm install --production
}

# Run database migrations
migrate_database() {
    log_info "Running database migrations..."

    cd "$APP_DIR"

    # Clear caches first
    sudo -u www-data php artisan config:clear
    sudo -u www-data php artisan cache:clear
    sudo -u www-data php artisan view:clear

    # Run migrations with force flag for production
    sudo -u www-data php artisan migrate --force

    # Seed database if needed
    if [ "$2" = "--seed" ]; then
        log_info "Running database seeders..."
        sudo -u www-data php artisan db:seed --class=BidangSeeder --force
        sudo -u www-data php artisan db:seed --class=UserSeeder --force
        sudo -u www-data php artisan db:seed --class=WelcomeMessageSeeder --force
    fi
}

# Optimize application
optimize_application() {
    log_info "Optimizing application..."

    cd "$APP_DIR"

    # Clear and cache configuration
    sudo -u www-data php artisan config:cache
    sudo -u www-data php artisan route:cache
    sudo -u www-data php artisan view:cache

    # Build frontend assets
    sudo -u www-data npm run build

    # Set correct permissions
    sudo chown -R www-data:www-data "$APP_DIR"
    sudo chmod -R 755 "$APP_DIR"
    sudo chmod -R 777 "$APP_DIR/storage"
    sudo chmod -R 777 "$APP_DIR/bootstrap/cache"
}

# Restart services
restart_services() {
    log_info "Restarting services..."

    # Restart PHP-FPM
    if check_service "$PHP_SERVICE"; then
        sudo systemctl restart "$PHP_SERVICE"
        log_info "PHP-FPM restarted"
    fi

    # Restart Nginx
    if check_service "$NGINX_SERVICE"; then
        sudo systemctl restart "$NGINX_SERVICE"
        log_info "Nginx restarted"
    fi

    # Restart Supervisor if exists
    if check_service "supervisor"; then
        sudo supervisorctl restart all
        log_info "Supervisor workers restarted"
    fi
}

# Health check
health_check() {
    log_info "Performing health check..."

    # Check if application is responding
    if curl -f -s -o /dev/null -w "%{http_code}" http://localhost | grep -E "(200|302)" > /dev/null; then
        log_info "Application is responding correctly"
    else
        log_warning "Application might not be responding correctly"
    fi

    # Check Laravel health route if available
    if curl -f -s -o /dev/null -w "%{http_code}" http://localhost/up | grep "200" > /dev/null; then
        log_info "Laravel health check passed"
    fi
}

# Cleanup old backups (keep last 5)
cleanup_backups() {
    log_info "Cleaning up old backups..."

    if [ -d "$BACKUP_DIR" ]; then
        cd "$BACKUP_DIR"
        ls -t | tail -n +6 | xargs -r rm -rf
        log_info "Old backups cleaned up"
    fi
}

# Main deployment flow
main() {
    log_info "Starting Laravel application deployment..."

    create_backup
    update_code
    install_dependencies
    migrate_database $*
    optimize_application
    restart_services
    health_check
    cleanup_backups

    log_info "🎉 Deployment completed successfully!"
    log_warning "Please verify your application is working correctly"
}

# Show usage
if [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    echo "Usage: $0 [branch] [--seed]"
    echo "Example: $0 main --seed"
    echo "Default branch: main"
    exit 0
fi

# Run main function
main $*