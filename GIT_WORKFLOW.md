# Git Workflow untuk Deployment

## 🚀 Quick Start Guide

### 1. Setup Git Repository (Pertama Kali)

#### A. Buat Repository di GitHub/GitLab/Bitbucket
1. Login ke GitHub (atau GitLab/Bitbucket)
2. Klik "New Repository"
3. Nama: `ticketing` atau sesuai keinginan
4. Pilih: **Private** (untuk keamanan)
5. Jangan centang "Initialize with README" (karena sudah ada di local)
6. Klik "Create Repository"

#### B. Connect Local ke Remote Repository

```bash
# Di Windows PowerShell atau Git Bash
cd d:\laragon\www\ticketing

# Initialize git (jika belum)
git init

# Add semua file
git add .

# Commit pertama
git commit -m "Initial commit - Laravel Ticketing System"

# Add remote (ganti URL dengan repository Anda)
git remote add origin https://github.com/username/ticketing.git

# Push ke GitHub
git push -u origin main
```

**Jika branch default adalah 'master' bukan 'main':**
```bash
git branch -M main
git push -u origin main
```

---

## 📝 Daily Workflow (Setelah Setup)

### Saat Membuat Perubahan di Local

```bash
# 1. Cek status file yang berubah
git status

# 2. Add file yang ingin di-commit
git add .
# Atau add file spesifik:
# git add app/Http/Controllers/TicketController.php

# 3. Commit dengan pesan yang jelas
git commit -m "Add: fitur export ticket ke Excel"

# 4. Push ke repository
git push origin main
```

### Contoh Pesan Commit yang Baik:
```bash
git commit -m "Add: fitur notifikasi email untuk ticket baru"
git commit -m "Fix: bug pada filter tanggal di riwayat ticket"
git commit -m "Update: tampilan dashboard IT dengan card statistics"
git commit -m "Remove: file debug yang tidak diperlukan"
```

---

## 🖥️ Deploy ke Server Ubuntu

### Deployment Pertama Kali

```bash
# 1. SSH ke server
ssh username@your-server-ip

# 2. Clone repository
cd /var/www
sudo git clone https://github.com/username/ticketing.git ticketing
sudo chown -R $USER:$USER /var/www/ticketing

# 3. Setup environment
cd /var/www/ticketing
cp .env.production .env
nano .env  # Edit konfigurasi

# 4. Deploy
chmod +x deploy.sh
./deploy.sh

# 5. Generate app key
php artisan key:generate
```

### Update Aplikasi di Server (Setelah Push Perubahan)

```bash
# 1. SSH ke server
ssh username@your-server-ip

# 2. Pull perubahan terbaru
cd /var/www/ticketing
git pull origin main

# 3. Jalankan deployment script
./deploy.sh
```

**Script `deploy.sh` akan otomatis:**
- Install/update Composer dependencies
- Install/update NPM dependencies
- Build frontend assets
- Run database migrations
- Clear dan cache ulang Laravel
- Set permissions yang benar

---

## 🔐 Setup SSH Key (Opsional - Lebih Aman)

### Di Windows (Local)

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"

# Copy public key
cat ~/.ssh/id_ed25519.pub
# Atau di Windows:
type C:\Users\YourUsername\.ssh\id_ed25519.pub
```

### Di GitHub
1. Settings → SSH and GPG keys
2. New SSH key
3. Paste public key
4. Save

### Gunakan SSH URL untuk Clone
```bash
# Ganti HTTPS dengan SSH
git remote set-url origin git@github.com:username/ticketing.git
```

---

## 🌿 Branching Strategy (Opsional - untuk Tim)

### Jika Bekerja dengan Tim:

```bash
# Buat branch untuk fitur baru
git checkout -b feature/export-excel

# Kerjakan fitur...
git add .
git commit -m "Add: export Excel functionality"

# Push branch
git push origin feature/export-excel

# Merge ke main setelah review
git checkout main
git merge feature/export-excel
git push origin main
```

### Branch Recommendations:
- `main` - Production (yang ada di server)
- `development` - Development/testing
- `feature/nama-fitur` - Fitur baru
- `fix/nama-bug` - Bug fixes

---

## ⚠️ Troubleshooting

### Error: "fatal: remote origin already exists"
```bash
# Hapus remote lama
git remote remove origin

# Add remote baru
git remote add origin https://github.com/username/ticketing.git
```

### Error: "Updates were rejected because the tip of your current branch is behind"
```bash
# Pull dulu sebelum push
git pull origin main --rebase
git push origin main
```

### Lupa Password GitHub (HTTPS)
Gunakan **Personal Access Token** sebagai password:
1. GitHub → Settings → Developer settings → Personal access tokens
2. Generate new token (classic)
3. Pilih scopes: `repo` (full control)
4. Copy token dan gunakan sebagai password saat push

### File Besar Tidak Bisa Di-push
```bash
# Check ukuran file
git ls-files -s | sort -k4 -n -r | head -10

# Jika ada file yang terlalu besar, add ke .gitignore
echo "nama-file-besar.zip" >> .gitignore
git rm --cached nama-file-besar.zip
git commit -m "Remove large file"
```

---

## 📊 Useful Git Commands

```bash
# Lihat history commit
git log --oneline

# Lihat perubahan yang belum di-commit
git diff

# Lihat perubahan file tertentu
git diff app/Http/Controllers/TicketController.php

# Undo perubahan yang belum di-commit
git checkout -- nama-file.php

# Undo commit terakhir (tapi keep changes)
git reset --soft HEAD~1

# Lihat remote URL
git remote -v

# Update dari remote
git fetch origin
git pull origin main

# Lihat branch
git branch -a

# Hapus file dari Git tapi keep di local
git rm --cached nama-file
```

---

## 🎯 Best Practices

1. **Commit Sering**: Jangan tunggu terlalu banyak perubahan
2. **Pesan Commit Jelas**: Jelaskan apa yang diubah dan kenapa
3. **Pull Sebelum Push**: Selalu pull dulu untuk menghindari conflict
4. **Jangan Commit File Sensitif**: `.env`, password, API keys
5. **Test Sebelum Push**: Pastikan kode berjalan di local
6. **Gunakan .gitignore**: Jangan commit file yang tidak perlu

---

## 📞 Bantuan

Jika ada masalah dengan Git:
1. Cek status: `git status`
2. Cek log: `git log --oneline`
3. Google error message
4. Hubungi IT Team

**Dokumentasi Resmi:**
- Git: https://git-scm.com/doc
- GitHub: https://docs.github.com
- GitLab: https://docs.gitlab.com
