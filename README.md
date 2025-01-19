# Sehatea POS

Sehatea POS adalah aplikasi Point of Sale yang dirancang untuk UMKM (Usaha Mikro, Kecil, dan Menengah) yang mengkhususkan diri dalam produk es teh. Aplikasi ini dibuat untuk mengelola transaksi penjualan secara efisien.

## Teknologi yang Digunakan

-   Laravel 11
-   Livewire 3
-   Node.js dan NPM

## Prasyarat

Sebelum Anda memulai, pastikan Anda telah memenuhi persyaratan berikut:

-   PHP >= 8.1
-   Composer
-   Node.js dan NPM
-   MySQL atau database yang kompatibel

## Instalasi

Ikuti langkah-langkah berikut untuk menyiapkan lingkungan pengembangan Anda:

1. Klon repositori

    ```
    git clone https://github.com/agungsapp/sehatea_apriori.git
    ```

2. Pindah ke direktori proyek

    ```
    cd sehatea_apriori
    ```

3. Instal dependensi PHP

    ```
    composer install
    ```

4. Instal dependensi JavaScript

    ```
    npm install
    ```

5. Salin file .env.example dan ubah namanya menjadi .env

    ```
    cp .env.example .env
    ```

6. Konfigurasikan file .env Anda dengan kredensial database dan pengaturan lain yang diperlukan

7. Generate kunci aplikasi

    ```
    php artisan key:generate
    ```

8. Jalankan migrasi database dan isi dengan data awal
    ```
    php artisan migrate --seed
    ```

## Menjalankan Aplikasi

Anda dapat menjalankan aplikasi dengan dua cara:

### Mode Pengembangan

1. Di satu terminal, jalankan server pengembangan Laravel:

    ```
    php artisan serve
    ```

2. Di terminal lain, kompilasi dan muat ulang aset secara otomatis:
    ```
    npm run dev
    ```

### Mode Produksi

1. Build aset untuk produksi:

    ```
    npm run build
    ```

2. Jalankan server Laravel:
    ```
    php artisan serve
    ```

## Catatan Tambahan

-   Aplikasi ini dirancang khusus untuk mengelola transaksi penjualan Sehatea, sebuah UMKM es teh.
-   Untuk masalah atau saran, silakan buka issue di repositori GitHub.
