# Guía de Despliegue: F1 Speed en AWS EC2 (Ubuntu)

Esta documentación detalla los pasos para desplegar el proyecto en una instancia EC2 de AWS.

## 1. Configuración de la Instancia EC2
- **SO:** Ubuntu 22.04 LTS o superior.
- **Tipo:** t3.medium (recomendado para compilación de Assets) o t2.micro (mínimo).
- **Security Group (Puertos):**
  - `80` (HTTP)
  - `443` (HTTPS)
  - `22` (SSH)
  - `20777` (UDP) <- **CRÍTICO:** Puerto para la telemetría del juego F1 24.

## 2. Preparación del Servidor
Conéctate por SSH y ejecuta:

```bash
# 1. Actualizar sistema e instalar PHP (Ubuntu 26.04+ ya incluye PHP moderno)
sudo apt update && sudo apt upgrade -y
sudo apt install -y php-fpm php-mysql php-xml php-curl php-mbstring php-zip php-gd php-intl

# 2. Instalar Nginx y MySQL
sudo apt install -y nginx mysql-server

# Instalar Node.js (v18+) y NPM
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Despliegue del Código
```bash
cd /var/www
sudo mkdir -p TFG_F1_FEED
sudo chown $USER:$USER TFG_F1_FEED
cd TFG_F1_FEED

# Clona tu repositorio
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git f1_speed
cd f1_speed

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Instalar dependencias JS y compilar para producción
# IMPORTANTE: Los nombres de carpetas en Linux son sensibles a mayúsculas (Case-sensitive).
# Asegúrate de que tus imports en React coincidan exactamente con el nombre de las carpetas.
npm install
npm run build
```

## 4. Configuración del Entorno (.env)
Copia el archivo de ejemplo y edita las credenciales:
```bash
cp .env.example .env
php artisan key:generate
```
**Campos críticos en `.env`:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-ip-o-dominio

DB_DATABASE=f1_speed
DB_USERNAME=f1_user
DB_PASSWORD=tu_password_segura

QUEUE_CONNECTION=database
```

## 5. Base de Datos
```bash
sudo mysql -u root
# Dentro de MySQL:
CREATE DATABASE f1_speed;
CREATE USER 'f1_user'@'localhost' IDENTIFIED BY '861221AL_f1speed';
GRANT ALL PRIVILEGES ON f1_speed.* TO 'f1_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Ejecutar migraciones
php artisan migrate --force
```

## 6. Permisos y Nginx
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Crear enlace simbólico para imágenes
php artisan storage:link
```

**Configuración de Nginx:**
Crea `/etc/nginx/sites-available/f1-speed`:
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /var/www/TFG_F1_FEED/f1_speed/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Activa el sitio:
```bash
sudo ln -s /etc/nginx/sites-available/f1-speed /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 7. Script de Telemetría (Python)
Para que el script funcione, instala las librerías necesarias mediante el gestor de paquetes del sistema (en versiones nuevas de Ubuntu no se permite usar `pip3` globalmente):
```bash
sudo apt update
sudo apt install -y python3-requests python3-dotenv
```

Para que el script no se detenga, usa **Supervisor**:
```bash
sudo apt install supervisor
```
Crea `/etc/supervisor/conf.d/f1-telemetry.conf`:
```ini
[program:f1-telemetry]
process_name=%(program_name)s
command=python3 /var/www/TFG_F1_FEED/f1_speed/scripts/f1_24_real_telemetry.py
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/TFG_F1_FEED/f1_speed/storage/logs/telemetry.log
```
Activa el proceso:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start f1-telemetry
```

## 8. SSL (Opcional pero recomendado)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com
```
