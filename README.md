# Pos Bengkel M3

Repository utama project aplikasi POS (Poin of sale) bengkel M3

## Petunjuk Update

## Petunjuk Update

Sebelum melakukan langkah di bawah pastikan xampp sudah di buka dan start apache dan mysql

1. Ambil perubahan terbaru

```bash
  git pull origin main
```

tunggu hingga selesai.

2. install dependency

```bash
  composer install
```

tunggu hingga selesai

3. Migrasi ulang database dengan data dummy

```bash
  php artisan migrate:fresh --seed
```

## Menjalankan Project

1. buka gitbash dan paste

```bash
  npm run dev
```

2. menjalankan php server

```bash
  php artisan serve
```

akses web melalui

```bash
http://localhost:8000/
```

## Akses login

username :

```bash
administrator@gmail.com
```

password :

```bash
admin123
```
