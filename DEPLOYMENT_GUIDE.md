# Panduan Deployment Laravel ke Ubuntu Server

## Persiapan Sebelum Upload

### 1. File yang TIDAK perlu di-upload (sudah ada di .gitignore):
- `node_modules/` - akan di-install ulang di server
- `vendor/` - akan di-install ulang di server
- `.env` - gunakan `.env.production` sebagai template
- `storage/framework/cache/*`
- `storage/framework/sessions/*`
- `storage/framework/views/*`
- `storage/logs/*`

### 2. Pastikan file berikut ada:
- `.env.production` (template untuk production)
- `deploy.sh` (script deployment)
- `composer.json` dan `composer.lock`
- `package.json` dan `package-lock.json`

## Langkah-langkah Deployment

### A. Persiapan Ubuntu Server

#### 1. Install Dependencies yang Diperlukan

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Nginx
sudo apt install nginx -y

# Install PHP 8.1 dan extensions yang diperlukan
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-gd -y

# Install MySQL
sudo apt install mysql-server -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js dan NPM (versi LTS)
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install nodejs -y
```

#### 2. Setup MySQL Database

```bash
# Login ke MySQL
sudo mysql

# Jalankan perintah SQL berikut:
CREATE DATABASE ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ticketing_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
GRANT ALL PRIVILEGES ON ticketing.* TO 'ticketing_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Buat Direktori untuk Project

```bash
# Buat direktori
sudo mkdir -p /var/www/ticketing

# Set ownership
sudo chown -R $USER:$USER /var/www/ticketing
```

### B. Upload Project via WinSCP

#### 1. Koneksi WinSCP
- **Host**: IP Server Ubuntu Anda
- **Port**: 22
- **Username**: username SSH Anda
- **Password**: password SSH Anda
- **Protocol**: SFTP

#### 2. Upload Files
- Upload semua file KECUALI: `node_modules/`, `vendor/`, `.env`
- Upload ke direktori: `/var/www/ticketing`

#### 3. File yang Harus Di-upload:
```
/var/www/ticketing/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── .env.production
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── deploy.sh
├── vite.config.js
└── README.md
```

### C. Konfigurasi di Server

#### 1. Setup Environment File

```bash
# SSH ke server
ssh username@your-server-ip

# Masuk ke direktori project
cd /var/www/ticketing

# Copy dan edit .env
cp .env.production .env
nano .env

# Edit konfigurasi berikut:
# - DB_PASSWORD: password database yang Anda buat
# - APP_KEY: jika kosong, akan di-generate nanti
# - APP_URL: sesuaikan dengan domain Anda
```

#### 2. Jalankan Deployment Script

```bash
# Berikan permission execute
chmod +x deploy.sh

# Jalankan script
./deploy.sh
```

#### 3. Generate Application Key (jika belum ada)

```bash
php artisan key:generate
```

#### 4. Konfigurasi Nginx

```bash
# Buat file konfigurasi Nginx
sudo nano /etc/nginx/sites-available/ticketing
```

Paste konfigurasi berikut:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name ktu-it-ticketing.site www.ktu-it-ticketing.site;
    root /var/www/ticketing/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 5. Aktifkan Site dan Restart Nginx

```bash
# Buat symbolic link
sudo ln -s /etc/nginx/sites-available/ticketing /etc/nginx/sites-enabled/

# Test konfigurasi Nginx
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Enable Nginx untuk start otomatis
sudo systemctl enable nginx
```

#### 6. Set Permissions Final

```bash
cd /var/www/ticketing

# Set ownership
sudo chown -R www-data:www-data /var/www/ticketing

# Set permissions
sudo chmod -R 755 /var/www/ticketing
sudo chmod -R 775 /var/www/ticketing/storage
sudo chmod -R 775 /var/www/ticketing/bootstrap/cache
```

### D. Setup SSL (Opsional tapi Disarankan)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate SSL Certificate
sudo certbot --nginx -d ktu-it-ticketing.site -d www.ktu-it-ticketing.site

# Auto-renewal sudah otomatis di-setup oleh Certbot
```

## Troubleshooting

### 1. Permission Denied Error
```bash
sudo chown -R www-data:www-data /var/www/ticketing
sudo chmod -R 775 /var/www/ticketing/storage
sudo chmod -R 775 /var/www/ticketing/bootstrap/cache
```

### 2. 500 Internal Server Error
```bash
# Check error log
sudo tail -f /var/log/nginx/error.log

# Check Laravel log
sudo tail -f /var/www/ticketing/storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 3. Database Connection Error
- Pastikan MySQL berjalan: `sudo systemctl status mysql`
- Cek kredensial di file `.env`
- Test koneksi: `php artisan migrate:status`

### 4. Assets Tidak Muncul
```bash
# Build ulang assets
npm run build

# Clear cache
php artisan view:clear
```

## Maintenance Commands

### Update Aplikasi
```bash
cd /var/www/ticketing
git pull origin main  # jika menggunakan git
./deploy.sh
```

### Backup Database
```bash
mysqldump -u ticketing_user -p ticketing > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Monitor Logs
```bash
# Laravel logs
tail -f /var/www/ticketing/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

## Checklist Deployment

- [ ] Server Ubuntu sudah terinstall dan bisa diakses via SSH
- [ ] PHP 8.1, Nginx, MySQL, Composer, Node.js sudah terinstall
- [ ] Database MySQL sudah dibuat
- [ ] Project sudah di-upload via WinSCP
- [ ] File `.env` sudah dikonfigurasi dengan benar
- [ ] Script `deploy.sh` sudah dijalankan
- [ ] Nginx sudah dikonfigurasi dan restart
- [ ] Permissions sudah di-set dengan benar
- [ ] Website bisa diakses via browser
- [ ] SSL Certificate sudah terinstall (opsional)

## Kontak Support
Jika ada masalah, hubungi IT Team:
- ferdinal.sukman@ktushipyard.com
- irvanronaldi2@gmail.com
