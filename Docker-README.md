# ICT API - Docker Setup

Panduan instalasi dan menjalankan aplikasi menggunakan Docker.

## Prasyarat

Pastikan sudah terinstall:
- **Docker Desktop** (https://docs.docker.com/desktop/)

## Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd ict-api

# 2. Generate APP_KEY
cp .env.docker .env
# atau jalankan:
openssl rand -base64 32 > .env.key

# 3. Bangun dan jalankan semua service
docker compose up -d --build

# 4. Akses aplikasi
# Aplikasi: http://localhost:8000
# MySQL:    http://localhost:3306
# Redis:    http://localhost:6379
```

## Struktur Docker

| Service | Port | Port Container | Volume |
|---------|------|----------------|--------|
| **App** | 8000 | 8000 | Mount kode source |
| **MySQL** | 3306 | 3306 | mysql-data |
| **Redis** | 6379 | 6379 | redis-data |

## Stop dan Hapus

```bash
# Stop semua service
docker compose down

# Stop + hapus data volume
docker compose down -v
```

## Development vs Production

**Development** (default - gunakan `docker compose up -d`):
- Mengubah kode langsung di filesystem akan ter-refresh otomatis
- Volume mount: `.:/var/www/html`

**Production** (gunakan override file):
- Lebih stabil dan optimal
- Gunakan `Dockerfile` dengan `--no-dev`

## Environment Variables

| Variable | Default | Keterangan |
|----------|---------|-----------|
| APP_ENV | production | Mode aplikasi |
| APP_KEY | auto-generate | Enkripsi Laravel |
| DB_CONNECTION | mysql | Tipe database |
| DB_HOST | mysql | Host database |
| DB_DATABASE | forge | Nama database |
| DB_USERNAME | forge | Username database |
| DB_PASSWORD | forge | Password database |
| REDIS_HOST | redis | Host Redis |
| REDIS_PASSWORD | (kosong) | Password Redis |
| MYSQL_ROOT_PASSWORD | forge | Root MySQL |

## Troubleshooting

Jika error `APP_KEY`, ubah `.env` dan jalankan:
```bash
docker compose down
docker compose up -d --build
```

Jika error `database connection`, cek apakah MySQL sudah ready:
```bash
docker compose logs mysql
```
