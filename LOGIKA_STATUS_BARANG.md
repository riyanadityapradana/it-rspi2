# Logika Status Barang - IT-RSPI

## 🔄 Logika Status Barang

### **Status Barang Berdasarkan Status Perbaikan**

1. **Jika Status Perbaikan = "Dapat Diperbaiki"**
   - Status Barang = **"Baik"** (Hijau)
   - Barang tetap dalam kondisi baik, hanya perlu perbaikan minor

2. **Jika Status Perbaikan = "Tidak Dapat Diperbaiki"**
   - Status Barang = **"Rusak"** (Merah)
   - Barang rusak total dan tidak bisa diperbaiki

## 🎯 Alur Kerja

### **Langkah 1: Input Kerusakan**
- Staff klik tombol **"Input Kerusakan"** (Merah)
- Modal muncul dengan form:
  - Keterangan kerusakan
  - Status perbaikan (Dapat/Tidak Dapat Diperbaiki)

### **Langkah 2: Sistem Menentukan Status**
- **Jika pilih "Dapat Diperbaiki":**
  - Status barang tetap **"Baik"**
  - Muncul badge kuning "Dapat Diperbaiki"
  - Tombol "Input Kerusakan" hilang

- **Jika pilih "Tidak Dapat Diperbaiki":**
  - Status barang menjadi **"Rusak"**
  - Muncul badge merah "Tidak Dapat Diperbaiki"
  - Muncul tombol "Set Baik" (untuk perbaikan)

### **Langkah 3: Perbaikan (Opsional)**
- Jika barang "Tidak Dapat Diperbaiki" tapi ternyata bisa diperbaiki
- Staff klik tombol **"Set Baik"** (Hijau)
- Input keterangan perbaikan
- Status kembali menjadi **"Baik"**

## 📊 Tampilan di Tabel

### **Barang Baik (Normal)**
```
[Baik] (badge hijau)
```

### **Barang Baik + Dapat Diperbaiki**
```
[Baik] (badge hijau)
[Dapat Diperbaiki] (badge kuning)
Keterangan kerusakan...
```

### **Barang Rusak + Tidak Dapat Diperbaiki**
```
[Rusak] (badge merah)
[Tidak Dapat Diperbaiki] (badge merah)
Keterangan kerusakan...
```

## 🎨 Warna Badge

- **Status Barang:**
  - Baik = Hijau
  - Rusak = Merah

- **Status Perbaikan:**
  - Dapat Diperbaiki = Kuning
  - Tidak Dapat Diperbaiki = Merah

## 🔧 Keuntungan Logika Ini

1. **Fleksibilitas:** Barang dengan kerusakan minor tetap bisa digunakan
2. **Akurasi:** Status barang sesuai dengan kondisi sebenarnya
3. **Tracking:** Bisa melacak kerusakan dan perbaikan
4. **Efisiensi:** Tidak perlu mengubah status untuk kerusakan minor

## 📝 Contoh Penggunaan

### **Contoh 1: Kerusakan Minor**
- **Keterangan:** "Layar sedikit tergores"
- **Status Perbaikan:** "Dapat Diperbaiki"
- **Hasil:** Status tetap "Baik", bisa tetap digunakan

### **Contoh 2: Kerusakan Berat**
- **Keterangan:** "Motherboard terbakar"
- **Status Perbaikan:** "Tidak Dapat Diperbaiki"
- **Hasil:** Status menjadi "Rusak", perlu penggantian

### **Contoh 3: Perbaikan Berhasil**
- **Keterangan:** "Sudah diganti motherboard baru"
- **Status Perbaikan:** "Dapat Diperbaiki"
- **Hasil:** Status kembali "Baik" 