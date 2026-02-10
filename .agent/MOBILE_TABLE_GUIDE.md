# Mobile Responsive Table - Implementation Guide

## Overview
File `mobile-table.css` menyediakan styling universal untuk membuat semua tabel responsive dengan card-based layout di mobile.

## Cara Implementasi

### 1. Import CSS di Blade Template
Tambahkan di section `@push('styles')`:

```blade
@push('styles')
    @vite('resources/css/mobile-table.css')
@endpush
```

### 2. Tambahkan Class `mobile-card-table` pada Table Container
```html
<div class="table-responsive mobile-card-table">
    <table class="table table-hover">
        <!-- table content -->
    </table>
</div>
```

### 3. Tambahkan `data-label` pada Setiap `<td>`
```html
<tr>
    <td data-label="Ticket ID">#260210-001</td>
    <td data-label="Requester">John Doe</td>
    <td data-label="Status">
        <select class="form-select">...</select>
    </td>
    <td data-label="Action">
        <button class="btn btn-primary">View</button>
    </td>
</tr>
```

## Fitur

### Mobile (< 768px)
- ✅ Table header disembunyikan
- ✅ Setiap row menjadi card
- ✅ Label otomatis muncul dari `data-label`
- ✅ Kolom pertama di-emphasize (besar, bold, biru)
- ✅ Kolom terakhir (Action) dengan border top
- ✅ Form select full width
- ✅ Buttons responsive dengan flex layout

### Desktop (≥ 768px)
- ✅ Table normal dengan horizontal scroll
- ✅ Semua kolom terlihat

## Halaman yang Perlu Diupdate

1. ✅ `it/index-ticket.blade.php` - Sudah menggunakan custom card layout
2. ⏳ `it/IT.blade.php` - Dashboard IT (Recent Activity table)
3. ⏳ `staff/list-tiket.blade.php` - Staff ticket list
4. ⏳ `staff/staff.blade.php` - Staff dashboard
5. ⏳ `admin/tickets.blade.php` - Admin ticket management
6. ⏳ `it/riwayat-ticket.blade.php` - IT ticket history
7. ⏳ `it/feedbacks.blade.php` - Feedback list
8. ⏳ `admin/management-pengguna.blade.php` - User management

## Customization

### Mengubah Border Color
Border kiri card mengikuti priority/status. Tambahkan inline style:
```html
<tr style="border-left-color: #dc3545;"> <!-- Red for urgent -->
```

### Menyembunyikan Label Tertentu
```css
.mobile-card-table td[data-label="Internal"]:before {
    display: none;
}
```

### Custom Spacing
```css
@media (max-width: 767.98px) {
    .mobile-card-table tr {
        padding: 1.5rem; /* Increase padding */
        margin-bottom: 1.5rem; /* Increase gap */
    }
}
```

## Best Practices

1. **Selalu tambahkan `data-label`** - Tanpa ini, label tidak akan muncul di mobile
2. **Gunakan nama label yang jelas** - "Ticket ID" lebih baik dari "ID"
3. **Test di mobile** - Pastikan semua kolom terlihat dan readable
4. **Konsisten** - Gunakan class yang sama di semua halaman

## Troubleshooting

### Label tidak muncul
- Pastikan `data-label` attribute ada di setiap `<td>`
- Check browser cache, lakukan hard refresh (Ctrl+Shift+R)

### Card terlalu lebar
- Pastikan parent container tidak memiliki `overflow: hidden`
- Check apakah ada inline `width` yang conflict

### Buttons tidak responsive
- Pastikan buttons ada di kolom terakhir (`<td>`)
- Gunakan class `btn` dari Bootstrap
