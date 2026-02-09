-- ============================================
-- SQL Script: Update Regional Email (rmail)
-- ============================================
-- Purpose: Mengisi kolom rmail di tabel regions
-- Date: 2026-02-09
-- ============================================

-- Cek data regional yang ada
SELECT id, name, rmail FROM regions;

-- ============================================
-- UPDATE REGIONAL EMAIL
-- ============================================

-- Regional Jakarta
UPDATE regions 
SET rmail = 'it-jakarta@ktushipyard.com' 
WHERE name = 'Regional Jakarta';

-- Regional Sagulung
UPDATE regions 
SET rmail = 'it-sagulung@ktushipyard.com' 
WHERE name = 'Regional Sagulung';

-- Regional Marunda
UPDATE regions 
SET rmail = 'it-marunda@ktushipyard.com' 
WHERE name = 'Regional Marunda';

-- ============================================
-- VERIFIKASI HASIL UPDATE
-- ============================================

SELECT 
    id,
    name,
    rmail,
    updated_at
FROM regions
ORDER BY id;

-- ============================================
-- CONTOH: Jika ingin menggunakan email yang sama
-- ============================================

-- Semua regional menggunakan 1 email pusat
-- UPDATE regions SET rmail = 'it-support@ktushipyard.com';

-- ============================================
-- CONTOH: Jika ingin reset/kosongkan rmail
-- ============================================

-- UPDATE regions SET rmail = NULL WHERE name = 'Regional Jakarta';

-- ============================================
-- CEK RELASI: Regional dengan IT Staff
-- ============================================

SELECT 
    r.id as region_id,
    r.name as region_name,
    r.rmail as regional_email,
    u.name as it_staff_name,
    u.email as it_staff_email,
    u.role
FROM regions r
LEFT JOIN users u ON u.region_id = r.id AND u.role IN ('tim it', 'it')
ORDER BY r.id, u.name;

-- ============================================
-- CEK TOTAL EMAIL RECIPIENTS PER REGIONAL
-- ============================================

SELECT 
    r.id,
    r.name as regional,
    r.rmail as regional_email,
    COUNT(u.id) as total_it_staff,
    GROUP_CONCAT(u.email SEPARATOR ', ') as it_staff_emails
FROM regions r
LEFT JOIN users u ON u.region_id = r.id 
    AND u.role IN ('tim it', 'it')
    AND u.email IS NOT NULL
GROUP BY r.id, r.name, r.rmail
ORDER BY r.id;
