# 🎮 2D RPG Mobile Game

Mobile-First 2D RPG Game dengan map kota yang bisa di-explore menggunakan Virtual D-Pad controls.

## ✨ Fitur Lengkap

### Core Game Features:
1. ✅ **Full-Screen Mobile Canvas** - Auto-resize sesuai layar device
2. ✅ **Virtual D-Pad Controls** - Touch controls ⬆️⬇️⬅️➡️ untuk mobile
3. ✅ **Keyboard Support** - Arrow keys & WASD untuk desktop testing
4. ✅ **Camera Follow System** - Character tetap di tengah, map yang scroll
5. ✅ **Zoom System (1.3x default)** - Map zoom in agar tidak langsung kelihatan hitam
6. ✅ **Smart Camera Boundaries** - Atas/kiri/kanan bounded, bawah bebas (untuk D-pad space)
7. ✅ **Walking Animation** - Bounce effect + direction flip (kiri/kanan)
8. ✅ **Shadow Effect** - Shadow tidak ikut bounce
9. ✅ **Smooth Movement** - Responsive controls

### Technical Features:
10. ✅ **No Image Stretch** - Map & character ukuran asli (1:1 pixel dengan zoom)
11. ✅ **Boundary Detection** - Character tidak bisa keluar dari map
12. ✅ **Black Space Management** - Area di luar map = hitam
13. ✅ **Touch & Mouse Events** - Support berbagai input method

### Developer Tools:
14. ✅ **Debug Mode** dengan toggle button (pojok kanan atas)
15. ✅ **Click-to-Teleport** - Klik map untuk teleport character
16. ✅ **Real-time Coordinates Display** - Character & Camera X,Y
17. ✅ **Grid Overlay (optional)** - 25x25px grid
18. ✅ **Console Logging** - Koordinat auto-log untuk copy-paste
19. ✅ **Crosshair Indicator** - Red crosshair di center screen

## 🚀 Cara Menjalankan

1. Pastikan file `map.jpg` ada di folder yang sama
2. Buka `index.html` di browser
3. Game akan otomatis load dan siap dimainkan!

## 🎮 Kontrol

### Mobile:
- **Virtual D-Pad** di pojok kiri bawah
- **Debug Toggle** di pojok kanan atas (🐛)

### Desktop:
- **Arrow Keys** atau **WASD** untuk movement
- **Mouse Click** untuk teleport (jika debug mode ON)

## 🛠️ Debug Mode

Klik tombol 🐛 di pojok kanan atas untuk mengaktifkan:
- Real-time coordinates display
- Grid overlay (toggle dengan tombol Grid)
- Teleport mode (toggle dengan tombol Teleport)
- Crosshair indicator di center screen

## 📱 Mobile Optimization

- Touch events optimized untuk mobile
- Responsive design untuk berbagai ukuran layar
- Virtual D-pad dengan visual feedback
- Landscape mode support
- No image stretching, pixel-perfect rendering

## 🎨 Customization

Game menggunakan:
- **Map**: `map.jpg` (auto-detect ukuran)
- **Character**: Placeholder rectangle (bisa diganti dengan sprite)
- **Zoom**: 1.3x default (bisa diubah di code)
- **Speed**: 2 pixels per frame (bisa diubah di code)

## 🔧 Technical Details

- **Engine**: HTML5 Canvas + JavaScript
- **Rendering**: Pixel-perfect dengan zoom
- **Animation**: 60fps smooth movement
- **Input**: Multi-touch support
- **Camera**: Smooth follow dengan boundaries
- **Performance**: Optimized untuk mobile devices

## 📝 Notes

- Game akan otomatis detect ukuran map dari `map.jpg`
- Character boundary detection mencegah keluar dari map
- Camera boundaries mencegah area hitam di atas/kiri/kanan
- Bawah bebas untuk space virtual D-pad
- Console logging untuk debugging coordinates