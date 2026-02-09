# 📧 Email Notification System - Complete Guide

## Overview
Sistem notifikasi email sudah **AKTIF dan BERFUNGSI** untuk mengirim email otomatis pada 2 event penting:

1. ✅ **Email ke Tim IT** - Ketika user membuat tiket baru
2. ✅ **Email ke User** - Ketika tiket sudah closed/resolved

---

## 🔧 Konfigurasi Email (`.env`)

Email sudah dikonfigurasi menggunakan Gmail SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=ferdinal.sukman@ktushipyard.com
MAIL_PASSWORD=xpdsmydirgtysqir
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ferdinal.sukman@ktushipyard.com
MAIL_FROM_NAME="IT SUPPORT KTU"
```

### ⚠️ Catatan Penting:
- Password yang digunakan adalah **App Password** dari Gmail (bukan password akun biasa)
- Pastikan akun Gmail sudah mengaktifkan **2-Factor Authentication**
- App Password bisa dibuat di: https://myaccount.google.com/apppasswords

---

## 📨 1. Email ke Tim IT (Tiket Baru)

### Kapan Dikirim?
Email dikirim **otomatis** ketika user membuat tiket baru melalui dashboard staff.

### Siapa yang Menerima?
Email dikirim ke **Tim IT sesuai regional/lokasi user**:

1. **Jika ada IT yang assigned** (berdasarkan region_id user):
   - Email dikirim ke IT yang assigned saja
   
2. **Jika tidak ada IT assigned** (fallback):
   - Email dikirim ke **semua IT di regional yang sama**
   
3. **Logika Assignment**:
   ```php
   // Auto-assign berdasarkan location → region
   $assignedIt = User::whereIn('role', ['tim it', 'it'])
       ->where('region_id', $regionId)
       ->inRandomOrder()
       ->first();
   ```

### Kode Implementasi
**File**: `app/Http/Controllers/Staff/TicketController.php` (baris 328-358)

```php
// Kirim email notification
try {
    $recipients = [];

    // 1. Send to ASSIGNED IT (Primary)
    if ($assignedItId) {
        $assignedIt = User::find($assignedItId);
        if ($assignedIt && $assignedIt->email) {
            $recipients[] = $assignedIt->email;
        }
    } 
    // 2. If no specific IT assigned (fallback), send to All IT in that Region
    elseif ($regionId) {
         $recipients = User::whereIn('role', ['tim it', 'it'])
            ->where('region_id', $regionId)
            ->pluck('email')
            ->toArray();
    }

    if (!empty($recipients)) {
        Mail::to($recipients)->send(new TicketCreatedMail($ticket));
    }

} catch (\Exception $e) {
    Log::warning('Email ticket gagal dikirim', ['error' => $e->getMessage()]);
}
```

### Email Template
**File**: `resources/views/emails/new-ticket-notification.blade.php`

**Isi Email**:
- Subject: `🎫 Tiket Baru Diterima: [TICKET_ID]`
- Informasi yang ditampilkan:
  - Ticket ID
  - Requester (nama + department)
  - Category
  - Priority (dengan badge warna)
  - Status (WAITING)
  - Issue Description
  - Tombol "View & Process Ticket" → link ke `/it/tickets`

---

## 📨 2. Email ke User (Tiket Closed)

### Kapan Dikirim?
Email dikirim **otomatis** ketika Tim IT mengubah status tiket menjadi **"closed"**.

### Siapa yang Menerima?
Email dikirim ke **user yang membuat tiket** (ticket creator).

### Kode Implementasi
**File**: `app/Http/Controllers/IT/TicketController.php` (baris 331-338)

```php
// Send Email to User ONLY if Closed
if ($value === 'closed') {
    try {
        Mail::to($ticket->user->email)->send(new TicketResolvedMail($ticket));
    } catch (\Exception $e) {
        Log::error('Failed to send resolution email: ' . $e->getMessage());
    }
}
```

### Email Template
**File**: `resources/views/emails/ticket_resolved.blade.php`

**Isi Email**:
- Subject: `IT Ticket Resolved - [TICKET_ID]`
- Informasi yang ditampilkan:
  - Ticket ID
  - Status: CLOSED/RESOLVED
  - Resolution notes (jika ada)
  - Tanggal resolved
  - Informasi tiket lengkap

---

## 🧪 Testing Email Notification

### 1. Test Email ke Tim IT (Tiket Baru)

**Langkah**:
1. Login sebagai **Staff/User**
2. Buat tiket baru dari dashboard
3. Isi form:
   - Category: pilih kategori
   - Priority: pilih prioritas
   - Description: isi deskripsi masalah
4. Submit tiket

**Expected Result**:
- ✅ Tiket berhasil dibuat
- ✅ Email terkirim ke IT yang sesuai regional
- ✅ Email berisi detail tiket lengkap

**Cek Email**:
- Buka inbox email IT yang sesuai regional user
- Cari email dengan subject: `🎫 Tiket Baru Diterima: [TICKET_ID]`

---

### 2. Test Email ke User (Tiket Closed)

**Langkah**:
1. Login sebagai **Tim IT**
2. Buka tiket yang ingin di-close
3. Ubah status menjadi **"Closed"**
4. Isi resolution notes (opsional)
5. Save changes

**Expected Result**:
- ✅ Status tiket berubah menjadi "Closed"
- ✅ Email terkirim ke user pembuat tiket
- ✅ Email berisi konfirmasi tiket sudah diselesaikan

**Cek Email**:
- Buka inbox email user pembuat tiket
- Cari email dengan subject: `IT Ticket Resolved - [TICKET_ID]`

---

## 🔍 Troubleshooting

### Email Tidak Terkirim?

1. **Cek Log Laravel**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Cek Konfigurasi SMTP**:
   - Pastikan `.env` sudah benar
   - Test koneksi SMTP:
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```

3. **Cek App Password Gmail**:
   - Pastikan menggunakan App Password, bukan password biasa
   - Generate ulang App Password jika perlu

4. **Cek Email User/IT**:
   - Pastikan user memiliki email yang valid di database
   - Cek tabel `users` kolom `email`

5. **Cek Queue**:
   - Jika menggunakan queue, pastikan worker berjalan:
   ```bash
   php artisan queue:work
   ```
   - Saat ini menggunakan `QUEUE_CONNECTION=sync` (langsung kirim)

---

## 📝 Mailable Classes

### 1. TicketCreatedMail
**File**: `app/Mail/TicketCreatedMail.php`

```php
class TicketCreatedMail extends Mailable
{
    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('🎫 Tiket Baru Diterima: ' . $this->ticket->ticket_id)
                    ->view('emails.new-ticket-notification')
                    ->with(['ticket' => $this->ticket]);
    }
}
```

### 2. TicketResolvedMail
**File**: `app/Mail/TicketResolvedMail.php`

```php
class TicketResolvedMail extends Mailable
{
    public $ticket;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IT Ticket Resolved - ' . $this->ticket->ticket_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_resolved',
            with: ['ticket' => $this->ticket],
        );
    }
}
```

---

## 🎨 Customization

### Mengubah Template Email

1. **Edit Template**:
   - Tiket Baru: `resources/views/emails/new-ticket-notification.blade.php`
   - Tiket Closed: `resources/views/emails/ticket_resolved.blade.php`

2. **Styling**:
   - Gunakan inline CSS (sudah ada di template)
   - Email client tidak support external CSS

3. **Menambah Informasi**:
   - Akses data tiket via `$ticket` variable
   - Contoh: `{{ $ticket->user->location->name }}`

### Mengubah Subject Email

Edit di Mailable class:

```php
// TicketCreatedMail.php
$subject = sprintf('🎫 [URGENT] Tiket Baru: %s', $this->ticket->ticket_id);

// TicketResolvedMail.php
subject: '✅ Tiket Selesai - ' . $this->ticket->ticket_id,
```

---

## ✅ Checklist Implementasi

- [x] Konfigurasi SMTP Gmail di `.env`
- [x] Mailable class untuk tiket baru (TicketCreatedMail)
- [x] Mailable class untuk tiket closed (TicketResolvedMail)
- [x] Email template untuk tiket baru
- [x] Email template untuk tiket closed
- [x] Integrasi di Staff\TicketController@store
- [x] Integrasi di IT\TicketController@updateField
- [x] Logika regional assignment untuk IT
- [x] Error handling & logging

---

## 🚀 Next Steps (Opsional)

Jika ingin meningkatkan sistem email:

1. **Queue Email** (untuk performa lebih baik):
   ```env
   QUEUE_CONNECTION=database
   ```
   ```bash
   php artisan queue:table
   php artisan migrate
   php artisan queue:work
   ```

2. **Email untuk Status Lain**:
   - Email ketika status → "in_progress"
   - Email ketika status → "resolved" (beda dengan closed)

3. **Email Reminder**:
   - Reminder ke IT jika tiket belum direspons dalam X jam
   - Reminder ke user untuk feedback

4. **Email dengan Attachment**:
   - Attach screenshot/file dari tiket

5. **Email Notification Settings**:
   - User bisa enable/disable notifikasi
   - Pilih jenis notifikasi yang ingin diterima

---

## 📞 Support

Jika ada pertanyaan atau masalah:
1. Cek log di `storage/logs/laravel.log`
2. Test email manual via `php artisan tinker`
3. Verifikasi konfigurasi Gmail App Password

---

**Last Updated**: 2026-02-09
**Status**: ✅ AKTIF DAN BERFUNGSI
