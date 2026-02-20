# CSE_326_Web_project

## Zacharias Melauin 27185

## Apache Setup (Linux)

This project is served from:

`/srv/http/webeng/CSE_326_Web_project`

### 1. Install and start Apache + PHP

Arch/Manjaro:

```bash
sudo pacman -S apache php php-apache
sudo systemctl enable --now httpd
```

Debian/Ubuntu:

```bash
sudo apt update
sudo apt install apache2 libapache2-mod-php php
sudo systemctl enable --now apache2
```

### 2. Point Apache DocumentRoot to this project

Edit your Apache config:

- Arch: `/etc/httpd/conf/httpd.conf`
- Debian/Ubuntu: `/etc/apache2/sites-available/000-default.conf`

Set DocumentRoot to:

`/srv/http/webeng/CSE_326_Web_project`

For Arch `httpd.conf`, update both lines:

```apache
DocumentRoot "/srv/http/webeng/CSE_326_Web_project"
<Directory "/srv/http/webeng/CSE_326_Web_project">
    AllowOverride All
    Require all granted
</Directory>
```

### 3. Fix ACL/permissions (important)

If you get `403 Forbidden`, remove restrictive ACLs:

```bash
setfacl -Rb /srv/http/webeng/CSE_326_Web_project
```

Make sure directories are executable and files readable:

```bash
find /srv/http/webeng/CSE_326_Web_project -type d -exec chmod 755 {} \;
find /srv/http/webeng/CSE_326_Web_project -type f -exec chmod 644 {} \;
```

### 4. Test config and reload Apache

Arch:

```bash
sudo apachectl -t
sudo systemctl reload httpd
```

Debian/Ubuntu:

```bash
sudo apache2ctl -t
sudo systemctl reload apache2
```

### 5. Verify

```bash
curl -I http://127.0.0.1/
```

Expected status:

`HTTP/1.1 200 OK`
