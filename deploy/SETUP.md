# Production setup on DigitalOcean

Target: Ubuntu 22.04 LTS, 4 vCPU / 16 GB droplet (`g-4vcpu-16gb`).

## 1. System packages

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server redis-server unzip git \
  software-properties-common ca-certificates

# PHP 8.2
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-redis \
  php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd \
  php8.2-bcmath php8.2-intl php8.2-sockets

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 2. MySQL

```bash
sudo mysql_secure_installation

# Create app DB user (don't use root)
sudo mysql <<SQL
CREATE DATABASE IF NOT EXISTS election
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'elections_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON election.* TO 'elections_app'@'localhost';
FLUSH PRIVILEGES;
SQL

# Drop in tuned config
sudo cp deploy/mysql-elections.cnf /etc/mysql/mysql.conf.d/elections.cnf
sudo systemctl restart mysql

# Load the schema/data
mysql -u elections_app -p election < Dump20260426.sql
```

## 3. Application

```bash
sudo mkdir -p /var/www/elections
sudo chown $USER:$USER /var/www/elections
git clone <your-repo> /var/www/elections
cd /var/www/elections

composer install --no-dev --optimize-autoloader

# Copy production env
cp .env.production .env
nano .env                                # fill in passwords, APP_URL, etc.

php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads public/profile_picture
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
```

## 4. RoadRunner binary (Linux)

The `rr` binary committed in the repo is Windows-only. Replace it:

```bash
cd /var/www/elections
RR_VERSION=2024.3.1                      # check latest at https://github.com/roadrunner-server/roadrunner/releases
wget https://github.com/roadrunner-server/roadrunner/releases/download/v${RR_VERSION}/roadrunner-${RR_VERSION}-linux-amd64.tar.gz
tar -xzf roadrunner-${RR_VERSION}-linux-amd64.tar.gz --strip-components=1 -C . roadrunner-${RR_VERSION}-linux-amd64/rr
chmod +x rr
rm roadrunner-${RR_VERSION}-linux-amd64.tar.gz

# Use the production config
cp deploy/rr.production.yaml .rr.yaml
```

## 5. systemd service

Create `/etc/systemd/system/elections-octane.service`:

```ini
[Unit]
Description=Elections Octane (RoadRunner)
After=network.target mysql.service redis.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/elections
ExecStart=/var/www/elections/rr serve -c /var/www/elections/.rr.yaml
ExecReload=/usr/bin/php /var/www/elections/artisan octane:reload
Restart=on-failure
RestartSec=5
LimitNOFILE=65535

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now elections-octane
sudo systemctl status elections-octane
```

## 6. Nginx (TLS + reverse proxy to RoadRunner)

`/etc/nginx/sites-available/elections`:

```nginx
server {
    listen 80;
    server_name elections.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name elections.yourdomain.com;

    ssl_certificate     /etc/letsencrypt/live/elections.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/elections.yourdomain.com/privkey.pem;

    client_max_body_size 64M;             # Excel imports

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header Host              $host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/elections /etc/nginx/sites-enabled/
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d elections.yourdomain.com
sudo systemctl reload nginx
```

## 7. Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow "Nginx Full"
sudo ufw enable
```

## 8. Verify

```bash
# Workers actually running?
ps aux | grep octane:worker | wc -l       # expect 16

# MySQL connections OK?
mysql -u root -p -e "SHOW VARIABLES LIKE 'max_connections';"

# Redis up?
redis-cli ping                            # expect PONG

# App responding?
curl -I https://elections.yourdomain.com
```

## 9. After every code deploy

```bash
cd /var/www/elections
git pull
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload elections-octane    # graceful worker restart
```
