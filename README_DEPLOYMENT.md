# 🚀 Deployment ke Ubuntu Server - Summary

## ✅ File yang Sudah Disiapkan

Saya sudah mempersiapkan semua file yang diperlukan untuk deployment:

### 1. **`.env.production`**
   - Template environment untuk production server
   - Sudah disesuaikan untuk Linux (localhost, production settings)
   - ⚠️ **PENTING**: Edit `DB_PASSWORD` setelah clone di server!

### 2. **`deploy.sh`**
   - Script bash untuk otomasi deployment
   - Menghandle: permissions, composer, npm, migrations, caching
   - Jalankan dengan: `chmod +x deploy.sh && ./deploy.sh`

### 3. **`nginx.conf`**
   - Konfigurasi Nginx siap pakai
   - Sudah include: PHP-FPM, caching, security headers
   - Copy ke: `/etc/nginx/sites-available/ticketing`

### 4. **`DEPLOYMENT_GUIDE.md`**
   - Panduan lengkap deployment (Bahasa Indonesia)
   - Mencakup: setup server, clone Git, konfigurasi, troubleshooting

### 5. **`GIT_WORKFLOW.md`**
   - Panduan Git workflow lengkap
   - Setup repository, daily workflow, branching, troubleshooting

### 6. **`QUICK_REFERENCE.txt`**
   - Cheat sheet command Git & deployment
   - Format ASCII art, bisa di-print

### 7. **`.gitignore`** (Updated)
   - Sudah disesuaikan untuk production
   - `.env.production` AKAN di-commit (sebagai template)
   - `.env` TIDAK akan di-commit (berisi password asli)

---

## 📋 Langkah Cepat Deployment

### **STEP 1: Push ke GitHub** (di Windows)

```powershell
cd d:\laragon\www\ticketing

# Add file baru
git add .

# Commit
git commit -m "Add deployment files for Ubuntu Server"

# Push ke GitHub
git push origin main
```

### **STEP 2: Setup Ubuntu Server**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install semua dependencies
sudo apt install nginx php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-gd mysql-server git nodejs npm -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Buat database
sudo mysql
```

```sql
CREATE DATABASE ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ticketing_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
GRANT ALL PRIVILEGES ON ticketing.* TO 'ticketing_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### **STEP 3: Clone & Deploy**

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/USERNAME/ticketing.git ticketing
sudo chown -R $USER:$USER /var/www/ticketing

# Setup environment
cd /var/www/ticketing
cp .env.production .env
nano .env  # Edit: DB_PASSWORD, APP_URL, dll

# Deploy
chmod +x deploy.sh
./deploy.sh

# Generate app key
php artisan key:generate
```

### **STEP 4: Setup Nginx**

```bash
# Copy konfigurasi
sudo cp nginx.conf /etc/nginx/sites-available/ticketing

# Enable site
sudo ln -s /etc/nginx/sites-available/ticketing /etc/nginx/sites-enabled/

# Test & restart
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### **STEP 5: Set Permissions**

```bash
sudo chown -R www-data:www-data /var/www/ticketing
sudo chmod -R 755 /var/www/ticketing
sudo chmod -R 775 /var/www/ticketing/storage
sudo chmod -R 775 /var/www/ticketing/bootstrap/cache
```

---

## 🔄 Update Aplikasi (Setelah Deployment Pertama)

### Di Windows (Local):
```powershell
cd d:\laragon\www\ticketing
git add .
git commit -m "Update: deskripsi perubahan"
git push origin main
```

### Di Server Ubuntu:
```bash
ssh username@server-ip
cd /var/www/ticketing
git pull origin main
./deploy.sh
```

---

## 🔐 Security Checklist

- [ ] Repository GitHub di-set **PRIVATE**
- [ ] File `.env` TIDAK di-commit (sudah di .gitignore)
- [ ] `.env.production` tidak berisi password asli
- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production` di server
- [ ] Database password kuat (min 16 karakter)
- [ ] Install SSL Certificate (gunakan Certbot)

---

## 📞 Jika Ada Masalah

### 1. Cek Log Error
```bash
# Nginx error log
sudo tail -f /var/log/nginx/error.log

# Laravel log
tail -f /var/www/ticketing/storage/logs/laravel.log
```

### 2. Clear Cache
```bash
cd /var/www/ticketing
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 3. Restart Services
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

---

## 📚 Dokumentasi

- **Panduan Lengkap**: Baca `DEPLOYMENT_GUIDE.md`
- **Git Workflow**: Baca `GIT_WORKFLOW.md`
- **Quick Reference**: Baca `QUICK_REFERENCE.txt`

---

## ⚡ Tips Pro

1. **Gunakan SSH Key** untuk GitHub (lebih aman dari password)
2. **Backup Database** sebelum update besar
3. **Test di Local** sebelum push ke production
4. **Monitor Log** setelah deployment
5. **Setup Auto-backup** database (cron job)

---

## 🎯 Next Steps

1. ✅ Push file deployment ke GitHub
2. ✅ Setup Ubuntu Server (install dependencies)
3. ✅ Clone repository di server
4. ✅ Konfigurasi .env
5. ✅ Run deploy.sh
6. ✅ Setup Nginx
7. ✅ Test website
8. ✅ Install SSL (opsional)

---

**Good luck dengan deployment! 🚀**

Jika ada pertanyaan, hubungi:
- ferdinal.sukman@ktushipyard.com
- irvanronaldi2@gmail.com
