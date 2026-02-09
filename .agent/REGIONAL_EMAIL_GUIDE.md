# 📧 Regional Email (rmail) Configuration Guide

## Overview
Sistem email notifikasi sekarang menggunakan **Regional Email (rmail)** untuk mengirim notifikasi tiket baru ke Tim IT berdasarkan regional.

---

## 🔄 Perubahan Sistem Email

### **SEBELUM** (Logika Lama):
- Email hanya dikirim ke IT yang di-assign
- Jika tidak ada IT assigned, kirim ke semua IT di regional

### **SEKARANG** (Logika Baru):
Email dikirim ke **2 sumber**:
1. ✅ **Regional Email (rmail)** - Email utama regional dari tabel `regions`
2. ✅ **Semua IT Staff** - Email individual semua Tim IT di regional yang sama

**Keuntungan**:
- Email regional bisa berupa **group email** atau **shared mailbox**
- Semua IT di regional tetap mendapat notifikasi di email pribadi mereka
- Lebih fleksibel dan tidak ada email yang terlewat

---

## 📊 Database Structure

### Tabel: `regions`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar | Nama regional (contoh: Regional Jakarta) |
| description | text | Deskripsi regional |
| **rmail** | varchar | **Email regional untuk notifikasi tiket** |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

---

## 🛠️ Cara Mengisi Data Regional Email

### Option 1: Via Database (phpMyAdmin/MySQL)

1. Buka **phpMyAdmin** atau MySQL client
2. Pilih database `ticketing`
3. Buka tabel `regions`
4. Edit setiap regional dan isi kolom `rmail`

**Contoh Data**:

```sql
UPDATE regions SET rmail = 'it-jakarta@ktushipyard.com' WHERE name = 'Regional Jakarta';
UPDATE regions SET rmail = 'it-sagulung@ktushipyard.com' WHERE name = 'Regional Sagulung';
UPDATE regions SET rmail = 'it-marunda@ktushipyard.com' WHERE name = 'Regional Marunda';
```

---

### Option 2: Via Laravel Tinker

```bash
php artisan tinker
```

```php
// Update satu per satu
$region = App\Models\Region::where('name', 'Regional Jakarta')->first();
$region->rmail = 'it-jakarta@ktushipyard.com';
$region->save();

// Atau update multiple sekaligus
App\Models\Region::where('name', 'Regional Jakarta')->update(['rmail' => 'it-jakarta@ktushipyard.com']);
App\Models\Region::where('name', 'Regional Sagulung')->update(['rmail' => 'it-sagulung@ktushipyard.com']);
App\Models\Region::where('name', 'Regional Marunda')->update(['rmail' => 'it-marunda@ktushipyard.com']);
```

---

### Option 3: Via Seeder (Recommended untuk Production)

Buat seeder untuk mengisi data regional email:

```bash
php artisan make:seeder RegionalEmailSeeder
```

Edit file `database/seeders/RegionalEmailSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionalEmailSeeder extends Seeder
{
    public function run()
    {
        $regionalEmails = [
            'Regional Jakarta' => 'it-jakarta@ktushipyard.com',
            'Regional Sagulung' => 'it-sagulung@ktushipyard.com',
            'Regional Marunda' => 'it-marunda@ktushipyard.com',
        ];

        foreach ($regionalEmails as $regionName => $email) {
            Region::where('name', $regionName)->update(['rmail' => $email]);
        }
    }
}
```

Jalankan seeder:

```bash
php artisan db:seed --class=RegionalEmailSeeder
```

---

## 📧 Format Email Regional

### Rekomendasi Format Email:

1. **Group Email** (Recommended):
   ```
   it-jakarta@ktushipyard.com
   it-sagulung@ktushipyard.com
   it-marunda@ktushipyard.com
   ```

2. **Shared Mailbox**:
   ```
   support-jakarta@ktushipyard.com
   helpdesk-sagulung@ktushipyard.com
   ```

3. **Multiple Recipients** (Pisahkan dengan koma):
   ```
   it1-jakarta@ktushipyard.com,it2-jakarta@ktushipyard.com
   ```
   **Note**: Jika menggunakan multiple recipients, perlu modifikasi kode untuk split by comma.

---

## 🔍 Cara Kerja Sistem Email Baru

### Flow Pengiriman Email:

```
User Create Ticket
    ↓
System Detect User's Location
    ↓
Get Region from Location
    ↓
Collect Email Recipients:
    1. Regional Email (rmail) dari tabel regions
    2. All IT Staff emails di regional yang sama
    ↓
Merge & Remove Duplicates
    ↓
Send Email to All Recipients
```

### Kode Implementation:

**File**: `app/Http/Controllers/Staff/TicketController.php`

```php
// ✅ NEW LOGIC: Prioritize Regional Email + All IT in Region
if ($regionId) {
    // 1. Get Regional Email (rmail) from region table
    $region = \App\Models\Region::find($regionId);
    if ($region && $region->rmail) {
        $recipients[] = $region->rmail;
    }

    // 2. Get ALL IT staff emails in this region
    $itEmails = User::whereIn('role', ['tim it', 'it'])
        ->where('region_id', $regionId)
        ->whereNotNull('email')
        ->pluck('email')
        ->toArray();
    
    // Merge regional email + IT staff emails (remove duplicates)
    $recipients = array_unique(array_merge($recipients, $itEmails));
}

// 3. Send email if we have recipients
if (!empty($recipients)) {
    Mail::to($recipients)->send(new TicketCreatedMail($ticket));
    Log::info("Ticket {$ticket->ticket_id} notification sent to: " . implode(', ', $recipients));
}
```

---

## ✅ Testing

### 1. Cek Data Regional Email

```sql
SELECT id, name, rmail FROM regions;
```

**Expected Output**:
```
+----+-------------------+--------------------------------+
| id | name              | rmail                          |
+----+-------------------+--------------------------------+
|  1 | Regional Jakarta  | it-jakarta@ktushipyard.com     |
|  2 | Regional Sagulung | it-sagulung@ktushipyard.com    |
|  3 | Regional Marunda  | it-marunda@ktushipyard.com     |
+----+-------------------+--------------------------------+
```

---

### 2. Test Email Notification

**Langkah**:
1. Pastikan `rmail` sudah diisi untuk regional yang akan ditest
2. Login sebagai **Staff/User** di lokasi tertentu (contoh: Jakarta)
3. Buat tiket baru
4. Cek log Laravel untuk melihat email recipients:

```bash
tail -f storage/logs/laravel.log
```

**Expected Log**:
```
[2026-02-09 14:58:00] local.INFO: Ticket TK-001 notification sent to: it-jakarta@ktushipyard.com, ferdinal.sukman@ktushipyard.com, irvanronaldi2@gmail.com
```

5. Cek inbox:
   - ✅ Regional email (`it-jakarta@ktushipyard.com`)
   - ✅ Email IT staff 1 (`ferdinal.sukman@ktushipyard.com`)
   - ✅ Email IT staff 2 (`irvanronaldi2@gmail.com`)

---

## 🎯 Example Scenarios

### Scenario 1: User di Jakarta Create Ticket

**Data**:
- User Location: Jakarta Pusat
- Region: Regional Jakarta
- Regional Email: `it-jakarta@ktushipyard.com`
- IT Staff di Regional Jakarta:
  - IT 1: `ferdinal.sukman@ktushipyard.com`
  - IT 2: `jakarta-it@ktushipyard.com`

**Email Recipients**:
```
1. it-jakarta@ktushipyard.com (regional email)
2. ferdinal.sukman@ktushipyard.com (IT staff 1)
3. jakarta-it@ktushipyard.com (IT staff 2)
```

---

### Scenario 2: Regional Email Kosong

**Data**:
- User Location: Sagulung
- Region: Regional Sagulung
- Regional Email: `NULL` (belum diisi)
- IT Staff di Regional Sagulung:
  - IT 1: `sagulung-it1@ktushipyard.com`
  - IT 2: `sagulung-it2@ktushipyard.com`

**Email Recipients**:
```
1. sagulung-it1@ktushipyard.com (IT staff 1)
2. sagulung-it2@ktushipyard.com (IT staff 2)
```

**Note**: Sistem tetap berfungsi meskipun `rmail` kosong. Email akan dikirim ke semua IT staff di regional.

---

## 🔧 Troubleshooting

### Problem 1: Email Tidak Terkirim ke Regional Email

**Cek**:
1. Apakah `rmail` sudah diisi di tabel `regions`?
   ```sql
   SELECT name, rmail FROM regions WHERE id = [region_id];
   ```

2. Apakah format email valid?
   - Harus format email yang benar: `name@domain.com`
   - Tidak boleh ada spasi atau karakter aneh

3. Cek log Laravel:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

### Problem 2: Duplicate Email

Jika regional email sama dengan email IT staff, sistem otomatis menghapus duplikat menggunakan `array_unique()`.

**Contoh**:
- Regional Email: `it-jakarta@ktushipyard.com`
- IT Staff 1: `it-jakarta@ktushipyard.com` (sama!)
- IT Staff 2: `other-it@ktushipyard.com`

**Result**: Email hanya dikirim 1x ke `it-jakarta@ktushipyard.com`

---

### Problem 3: Email Tidak Sampai ke Inbox

**Cek**:
1. **Spam Folder** - Email mungkin masuk ke spam
2. **Email Server** - Pastikan SMTP configuration benar
3. **Gmail App Password** - Pastikan masih valid
4. **Email Quota** - Cek apakah ada limit pengiriman email

---

## 📋 Checklist Setup

- [ ] Migration `add_rmail_to_regions_table` sudah dijalankan
- [ ] Kolom `rmail` sudah ada di tabel `regions`
- [ ] Model `Region` sudah include `rmail` di `$fillable`
- [ ] Data `rmail` sudah diisi untuk semua regional
- [ ] Test create ticket dan cek email terkirim
- [ ] Cek log Laravel untuk konfirmasi recipients
- [ ] Verifikasi email sampai ke inbox (regional + IT staff)

---

## 🚀 Next Steps (Optional)

1. **Admin Panel untuk Manage Regional Email**:
   - Buat halaman admin untuk edit `rmail` via UI
   - Tidak perlu manual via database

2. **Email Validation**:
   - Validasi format email saat input
   - Test email connection sebelum save

3. **Email Template Customization**:
   - Customize template per regional
   - Include regional-specific information

4. **Email Tracking**:
   - Log semua email yang terkirim
   - Dashboard untuk monitoring email delivery

---

**Last Updated**: 2026-02-09
**Status**: ✅ IMPLEMENTED & READY TO USE
