# 🏫 Website Monitoring Ekstrakurikuler (Eskul) — Multi-Role Access

A dynamic and role-based website for managing and monitoring extracurricular activities in a school or organization. Built using **HTML, CSS, JavaScript, PHP**, and **MySQL**, this platform allows various stakeholders—**Anggota**, **Pengurus**, **Pembina**, and **Super Admin**—to collaborate, track progress, and manage eskul activities seamlessly.

---

## 🚀 Features by Role

### 👤 Anggota
- 📅 Lihat jadwal latihan & agenda kegiatan  
- 📋 Lihat riwayat kehadiran pribadi  
- 📥 Upload tugas/kegiatan eskul (jika ada)  
- ✅ Konfirmasi kehadiran pada kegiatan  

---

### 🧑‍💼 Pengurus
- ✍️ Input dan update jadwal latihan  
- 📝 Rekap kehadiran anggota  
- 📤 Upload dokumentasi kegiatan  
- 📢 Buat pengumuman internal eskul  
- 🔍 Lihat data anggota & aktivitas  

---

### 👨‍🏫 Pembina
- 👀 Monitoring kegiatan yang dilakukan pengurus dan anggota  
- 📊 Lihat laporan kehadiran dan partisipasi  
- 📥 Approve kegiatan & agenda dari pengurus  
- 💬 Beri feedback atau catatan pembinaan  

---

### 🛡️ Super Admin
- 👑 Kelola seluruh user & role (buat/edit/hapus akun)  
- 🗃️ Kelola data master (jenis kegiatan, jadwal umum, dll)  
- 📊 Akses seluruh laporan kegiatan dan aktivitas eskul  
- 🔐 Hak penuh terhadap semua fitur & modul sistem  

---

## 🛠️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Optional Tools**: Chart.js (untuk grafik aktivitas), DataTables (untuk tabel interaktif)

---

## 📂 Folder Structure

📁 monitoring-eskul/  
├── 📁 css/               → Styling files  
├── 📁 js/                → JavaScript interaction  
├── 📁 php/               → Backend logic per modul  
├── 📁 views/             → Tampilan UI per role (anggota, pengurus, pembina, super_admin)  
├── 📁 uploads/           → File tugas, dokumentasi kegiatan  
├── 📄 index.php          → Halaman login  
├── 📄 dashboard.php      → Dashboard dinamis sesuai role  
├── 📄 db_config.php      → Koneksi database  
├── 📄 database.sql       → Struktur database  
└── 📄 README.md          → Dokumentasi proyek  

---

## ⚙️ Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/clementhermawan/Website-Monitoring-Eskul.git
```

### 2. Import database

- Buka `phpMyAdmin`  
- Buat database baru, misalnya `eskul_db`  
- Import file `database.sql`

### 3. Atur konfigurasi database

Edit file `db_config.php`:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "eskul_db";
```

### 4. Jalankan di server lokal

- Gunakan **XAMPP**, **WAMP**, atau **Laragon**  
- Taruh folder di dalam `htdocs`  
- Buka browser dan akses `http://localhost/monitoring-eskul/`

---

## 🧪 Contoh Role & User

| Role         | Username     | Password     |
|--------------|--------------|--------------|
| Anggota      | anggota1     | password123  |
| Pengurus     | pengurus1    | password123  |
| Pembina      | pembina1     | password123  |
| Super Admin  | admin        | admin123     |

> *Note: Gantilah password pada produksi untuk keamanan.*

---

## 📸 Screenshots

_Tambahkan screenshot dari tampilan dashboard anggota, pengurus, pembina, dan super admin._

---

## 📊 Laporan & Statistik

- Grafik kehadiran per kegiatan  
- Riwayat aktivitas masing-masing anggota  
- Log aktivitas pengurus (penjadwalan, unggahan dokumentasi)  
- Rekap partisipasi bulanan/tahunan  

---

## 📌 Future Enhancements

- [ ] Notifikasi otomatis via email atau WhatsApp  
- [ ] Modul sertifikat kegiatan  
- [ ] Kalender kegiatan interaktif  
- [ ] Multi-eskul management (jika ada lebih dari satu ekskul)

---

## 🙋‍♂️ Author

**Your Name**  
GitHub: [@clementhermawan](https://github.com/clementhermawan)

---

## 📄 License

This project is licensed under the MIT License – see the [LICENSE](https://github.com/clementhermawan/Licensi) file for details.

---
