#!/bin/bash
set -e

# Create upload directories if they don't exist
mkdir -p /var/www/html/uploads/products \
         /var/www/html/uploads/gallery \
         /var/www/html/uploads/activities \
         /var/www/html/uploads/content \
         /var/www/html/uploads/heroes \
         /var/www/html/uploads/media \
         /var/www/html/uploads/accommodation \
         /var/www/html/logs \
         /var/www/html/cache

# Fix permissions
chown -R www-data:www-data /var/www/html/uploads \
                           /var/www/html/logs \
                           /var/www/html/cache \
                           /var/www/html/config

chmod -R 775 /var/www/html/uploads \
             /var/www/html/logs \
             /var/www/html/cache

# Auto-import database schema on first deploy.
# Credenciais lidas do ambiente (definidas no Dokploy) — sem segredos no repositório.
DB_HOST="${DB_HOST:-alojamentogi-mysql-8g3t8r}"
DB_USER="${DB_USER:-casadogi_user}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-casadogi}"
SQL_FILE="/var/www/html/database/casadogiFinal.sql"

if [ -z "$DB_PASS" ]; then
    echo "[entrypoint] WARNING: DB_PASS não definido no ambiente — a saltar a importação automática da BD."
fi

echo "[entrypoint] Waiting for MySQL to be ready..."
MAX_TRIES=30
TRIES=0
until mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT 1" > /dev/null 2>&1; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "[entrypoint] WARNING: MySQL did not become ready in time — skipping auto-import."
        break
    fi
    echo "[entrypoint] MySQL not ready yet, retrying ($TRIES/$MAX_TRIES)..."
    sleep 2
done

if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT 1" > /dev/null 2>&1; then
    TABLE_COUNT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES" 2>/dev/null | wc -l)
    if [ "$TABLE_COUNT" -lt 5 ]; then
        echo "[entrypoint] Database is empty — importing schema from $SQL_FILE ..."
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"
        echo "[entrypoint] Database import complete."
    else
        echo "[entrypoint] Database already has $TABLE_COUNT table(s) — skipping import."
    fi
fi

# Start Apache
exec apache2-foreground
