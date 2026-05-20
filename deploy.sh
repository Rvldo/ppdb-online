#!/usr/bin/env bash
#
# deploy.sh — Deploy PPDB Online ke production server
#
# Usage:
#   ./deploy.sh              Deploy semua (code + build + migrate)
#   ./deploy.sh --quick      Deploy code saja (tanpa build & migrate)
#   ./deploy.sh --fresh      Fresh install (migrate:fresh --seed) ⚠️ HAPUS DATA
#
# Server: 51.79.146.182 (ubuntu)
# Path:   /var/www/ppdb
#

set -e

# ============================================================
# CONFIG — sesuaikan jika perlu
# ============================================================
SERVER_USER="ubuntu"
SERVER_IP="51.79.146.182"
SERVER_PATH="/var/www/ppdb"
SSH_TARGET="${SERVER_USER}@${SERVER_IP}"
BRANCH="main"

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# ============================================================
# FUNCTIONS
# ============================================================

info()    { echo -e "${CYAN}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

run_remote() {
    ssh -o StrictHostKeyChecking=no "${SSH_TARGET}" "$1"
}

# ============================================================
# PRE-FLIGHT CHECKS
# ============================================================

info "Checking SSH connection to ${SSH_TARGET}..."
ssh -o StrictHostKeyChecking=no -o ConnectTimeout=5 "${SSH_TARGET}" "echo ok" > /dev/null 2>&1 \
    || error "Tidak bisa connect ke ${SSH_TARGET}. Pastikan SSH key sudah di-setup."
success "SSH connection OK"

# ============================================================
# BUILD LOCAL (kecuali --quick)
# ============================================================

if [[ "$1" != "--quick" ]]; then
    info "Building frontend assets locally..."
    npm run build
    success "Build selesai"
fi

# ============================================================
# DEPLOY
# ============================================================

info "Deploying to ${SSH_TARGET}:${SERVER_PATH}..."
echo ""

run_remote "bash -s" <<'REMOTE_SCRIPT'
set -e

SERVER_PATH="/var/www/ppdb"
BRANCH="main"

cd "${SERVER_PATH}" || { echo "Directory ${SERVER_PATH} tidak ada!"; exit 1; }

echo ">>> Enabling maintenance mode..."
php artisan down --retry=30 || true

echo ">>> Pulling latest code from ${BRANCH}..."
git fetch origin
git reset --hard "origin/${BRANCH}"

echo ">>> Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo ">>> Installing npm dependencies..."
npm ci --production=false

echo ">>> Building frontend assets..."
npm run build

echo ">>> Running migrations..."
php artisan migrate --force

echo ">>> Clearing & caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ">>> Ensuring storage link..."
php artisan storage:link 2>/dev/null || true

echo ">>> Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo ">>> Restarting queue workers (if any)..."
php artisan queue:restart 2>/dev/null || true

echo ">>> Disabling maintenance mode..."
php artisan up

echo ""
echo "========================================="
echo "  DEPLOY SELESAI!"
echo "========================================="
REMOTE_SCRIPT

# ============================================================
# FRESH INSTALL (opsional)
# ============================================================

if [[ "$1" == "--fresh" ]]; then
    echo ""
    warn "⚠️  --fresh flag detected. Ini akan HAPUS SEMUA DATA dan reset database!"
    read -p "Yakin? (ketik 'yes' untuk lanjut): " confirm
    if [[ "$confirm" == "yes" ]]; then
        info "Running fresh migration + seed..."
        run_remote "cd ${SERVER_PATH} && php artisan migrate:fresh --seed --force"
        success "Fresh install selesai"
    else
        warn "Dibatalkan."
    fi
fi

echo ""
success "Deploy ke ${SERVER_IP} selesai!"
info "Website: http://${SERVER_IP}"
echo ""
