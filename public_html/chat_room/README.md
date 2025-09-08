# Chat Room Realtime (PHP + Firebase)

Aplikasi chat realtime untuk dosen dan mahasiswa.

## Fitur
- Auth user via MySQL (PHP sessions)
- Realtime messages via Firebase Realtime Database
- Upload & optimasi gambar via PHP GD

## Struktur
```
public_html/
  chat_room/
    assets/
      chat.js
      styles.css
    partials/
      header.php
      footer.php
    uploads/ (.htaccess mencegah eksekusi)
    chat.php
    config.php
    db.sql (schema users)
    index.php
    login.php
    logout.php
    register.php
    upload_image.php
```

## Konfigurasi
1) Import tabel users di phpMyAdmin
- Buka phpMyAdmin -> pilih DB `n1567943_chat-room-realtime_db`
- Import file `db.sql`

2) Sesuaikan kredensial database di `config.php`
- `$DB_HOST` biasanya `localhost` di cPanel
- `$DB_NAME`, `$DB_USER`, `$DB_PASS` sudah diisi sesuai data Anda

3) Firebase Realtime Database
- Buka `assets/chat.js` dan ganti `firebaseConfig` sesuai project Anda
- Pastikan database URL region sesuai (contoh asia-southeast1)
- Atur Realtime Database Rules (sementara untuk testing):
```
{
  "rules": {
    ".read": true,
    ".write": true
  }
}
```
  Setelah siap produksi, perketat rules berdasarkan auth (email/uid) atau domain.

4) PHP GD Extension
- Pastikan ekstensi GD aktif di cPanel (Select PHP Version -> gd diaktifkan)

5) Deploy ke cPanel
- Unggah folder `chat_room` ke `public_html` sehingga aplikasi berada di `https://multinteraktif.online/chat_room`

## Menggunakan Aplikasi
- Buka `/chat_room/register.php` untuk membuat akun
- Login lalu akses `/chat_room/chat.php`
- Pilih room, kirim pesan atau upload gambar; gambar dioptimasi otomatis

## Keamanan
- CSRF token untuk form auth
- `.htaccess` di `uploads` mencegah eksekusi file script
- Header keamanan dasar di `config.php`

## Catatan
- Ini menggunakan session PHP untuk auth (MySQL) dan Firebase untuk realtime saja
- Anda dapat menambahkan mapping message ke MySQL jika butuh historis non-realtime