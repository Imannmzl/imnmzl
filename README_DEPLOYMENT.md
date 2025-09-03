# 🚀 Deployment Guide - Hutan Harmoni Multiplayer

## 📋 Langkah-langkah Deployment

### 1. Setup Database
1. Login ke cPanel di hosting Anda
2. Buka **MySQL Databases**
3. Jalankan script SQL berikut di **phpMyAdmin**:

```sql
-- Copy isi file database.sql
```

### 2. Upload Files
Upload semua file ke folder: `multinteraktif.online/classpoint/html-package/HutanHarmoni/`

**Struktur folder:**
```
HutanHarmoni/
├── index.html
├── .htaccess
├── api/
│   ├── config.php
│   └── leaderboard.php
├── images/
│   └── [semua gambar]
├── audio/
│   └── [semua audio files]
└── database.sql
```

### 3. Test Database Connection
1. Buka: `multinteraktif.online/classpoint/html-package/HutanHarmoni/api/leaderboard.php?action=stats`
2. Harusnya return JSON dengan stats

### 4. Test Aplikasi
1. Buka: `multinteraktif.online/classpoint/html-package/HutanHarmoni/`
2. Isi nama dan kelas
3. Selesaikan petualangan
4. Cek leaderboard

## 🔧 Troubleshooting

### Database Connection Error
- Pastikan database credentials benar di `api/config.php`
- Pastikan database dan tabel sudah dibuat

### API Error
- Cek error logs di cPanel
- Pastikan PHP version >= 7.4
- Pastikan PDO extension enabled

### CORS Error
- Pastikan `.htaccess` sudah terupload
- Cek browser console untuk error details

## 📊 Features

### Multiplayer Features:
- ✅ Player registration dengan nama & kelas
- ✅ Real-time progress tracking
- ✅ Score calculation (100 - mistakes*10)
- ✅ Leaderboard ranking
- ✅ Statistics dashboard

### Leaderboard Display:
- 🥇🥈🥉 Top 3 dengan medal
- 📊 Total players, completion rate, average score
- 🔄 Real-time refresh
- 📱 Mobile responsive

## 🎯 Scoring System

**Score Formula:** `100 - (total_mistakes * 10)`
- Perfect run (0 mistakes) = 100 points
- 1 mistake = 90 points  
- 5 mistakes = 50 points
- 10+ mistakes = 0 points

**Ranking:** Score DESC → Mistakes ASC → Completion Time ASC