# Form pendaftaran Prisma Dana Gorontalo

Laravel 12 / PHP 8.2+, Blade, CSS, dan JavaScript tanpa kebutuhan build Node.js. Isian dan pilihan dropdown mengikuti referensi https://leadora.grenvio.com/daftar/0f10ec78a7ff.

## Menjalankan lokal

Dependensi dan database SQLite sudah disiapkan pada workspace ini:

```sh
php artisan serve --port=8127
```

Buka http://127.0.0.1:8127. Data tersimpan di tabel `registrations` dalam `database/database.sqlite`. File database dan `.env` tidak masuk Git. Form dapat digunakan tanpa JavaScript.

Untuk instalasi dari salinan baru, jalankan `composer install`, salin `.env.example` menjadi `.env`, lalu:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
php artisan serve --port=8127
```

## Form dan penyimpanan

Nama lengkap dan nomor HP wajib diisi, sesuai referensi. Kolom lain opsional: jenis kelamin, tanggal lahir, jenis usaha, lama usaha, omset bulanan, pinjaman berjalan, fasilitas, jumlah pinjaman diajukan, dan alamat usaha. Pilihan jenis usaha dan fasilitas tersedia di `app/Models/Registration.php`.

Nomor HP disimpan sebagai string untuk mempertahankan awalan nol; format `08`, `628`, atau `+628` diterima. Nominal diisi tanpa pemisah ribuan, maksimal dua angka desimal, dan disimpan sebagai `DECIMAL(14,2)`. Validasi berjalan di server dengan pesan bahasa Indonesia. Pengiriman menggunakan CSRF, batas 10 permintaan per menit per IP, dan redirect setelah berhasil. Isian dipertahankan jika ada kesalahan.

Halaman sukses mengonfirmasi penyimpanan. Aplikasi ini tidak mengirim WhatsApp atau otomatis menghubungkan data dengan Leadora. Data pendaftar hanya dapat ditinjau melalui halaman admin setelah login.

## Admin

Migrasi admin sudah diterapkan pada database workspace ini. Pada instalasi lain, jalankan `php artisan migrate` terlebih dahulu.

Buat akun admin dari seeder:

```sh
php artisan db:seed --class=AdminSeeder
```

Login menggunakan email `admin@prismadana.test` dan kata sandi `123456`. `php artisan db:seed` juga menjalankan seeder ini. Jika dijalankan ulang, seeder memperbarui akun dengan email tersebut dan mengatur ulang kata sandinya ke `123456` tanpa membuat akun duplikat.

Untuk membuat akun lain dengan email dan kata sandi pilihan sendiri, gunakan terminal:

```sh
php artisan admin:create
```

Masukkan email, kata sandi minimal 12 karakter (maksimal 72 byte), dan ulangi kata sandi. Input kata sandi disembunyikan. Perintah `admin:create` menolak email yang sudah terdaftar.

Untuk menentukan email dan nama saat membuat akun:

```sh
php artisan admin:create admin@example.com --name="Admin Prisma Dana"
```

Buka http://127.0.0.1:8127/admin/login setelah server berjalan. Admin menyediakan ringkasan jumlah pendaftar dan nominal pengajuan, daftar dengan pagination, pencarian nama/nomor HP/jenis usaha, filter tanggal, detail seluruh isian, ekspor CSV, serta logout. Ekspor mengikuti pencarian dan filter tanggal aktif, mencakup seluruh hasil filter, dan dapat dibuka di Excel; impor kolom nomor HP sebagai teks agar awalan nol tetap dipertahankan.

Hanya pengguna dengan peran `is_admin` yang bisa mengakses daftar, detail, dan ekspor. Login membatasi percobaan gagal, mengganti ID sesi saat berhasil, dan logout menghapus sesi. Halaman data admin menggunakan `Cache-Control: no-store`. Tidak ada halaman registrasi akun admin publik. Ekspor melindungi nilai dengan awalan formula agar diperlakukan sebagai teks oleh aplikasi spreadsheet.

## Menggunakan MySQL

Buat database MySQL terlebih dahulu, kemudian ubah `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=landing_page
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan `php artisan config:clear` dan `php artisan migrate`. Perubahan koneksi tidak memindahkan data SQLite lama secara otomatis.

## Verifikasi

```sh
php artisan test
```

Pengujian memakai SQLite di memori dan tidak menghapus database lokal. Mencakup form publik, penyimpanan dan validasi, otorisasi admin, login/logout, pembatasan percobaan login, pencarian dan tanggal, pagination, detail, ekspor CSV, serta pembuatan akun admin.

## Hosting

Arahkan document root server PHP ke direktori `public`, gunakan HTTPS, atur `APP_URL`, `APP_ENV=production`, dan `APP_DEBUG=false`, lalu jalankan `php artisan migrate --force` dan `php artisan optimize`. Direktori `storage` dan `bootstrap/cache` harus dapat ditulis oleh server; SQLite juga memerlukan akses tulis pada direktori `database`.

Tampilan berisi formulir Prisma Dana Gorontalo dalam panel putih dengan border dan font sistem. Aplikasi belum dipublikasikan ke hosting.
