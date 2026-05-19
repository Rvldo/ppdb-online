#!/usr/bin/env bash
#
# start.sh — bersihkan cache Laravel, build asset, lalu jalankan Vite dev server.
#
# Pakai dengan Laravel Valet: site sudah otomatis dilayani di http://ppdb.test
# sehingga tidak perlu `php artisan serve`.
#
# Tekan Ctrl+C untuk menghentikan Vite (file public/hot otomatis dihapus,
# Laravel akan kembali memakai asset hasil build).

set -e

cd "$(dirname "$0")"

echo "==> [1/5] Clear Laravel caches"
php artisan optimize:clear

echo "==> [2/5] Hapus public/hot bila tertinggal"
rm -f public/hot

echo "==> [3/5] npm install (kalau ada perubahan dependency)"
npm install --no-fund --no-audit

echo "==> [4/5] npm run build (asset produksi)"
npm run build

echo "==> [5/5] npm run dev (Vite + HMR) — buka http://ppdb.test"
exec npm run dev
