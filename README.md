# Tasks Manager
## Requirements
Descriptions|Versions
---|---
PHP|7.4.5
MySQL|8.0.17
Composer|1.9.0
Symfony|5.8.35

## Install symfony-cli
- curl -sS https://get.symfony.com/cli/installer | bash
- export PATH="$HOME/.symfony/bin:$PATH"
- mv /root/.symfony/bin/symfony /usr/local/bin/symfony

## Configuration
1. symfony check:requirements
2. git clone https://github.com/shid/tasks.git
3. Create/configure .env file
4. `composer install`
5. Create .htaccess at ./public folder and add:
    ```
    RewriteEngine On
    
    RewriteCond %{REQUEST_URI}::$0 ^(/.+)/(.*)::\2$
    RewriteRule .* - [E=BASE:%1]
    
    RewriteCond %{HTTP:Authorization} .+
    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]
    
    RewriteCond %{ENV:REDIRECT_STATUS} =""
    RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]
    
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ %{ENV:BASE}/index.php [L]
   ```
6. Create database with the name `admin_symfony_db`
7. `php bin/console doctrine:schema:create` 
8. `symfony server:start`

## Default Files
.env
```
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=

DATABASE_URL=mysql://user:password@127.0.0.1:3306/db_name
```

## Troubleshoot
- `php bin/console cache:clear`
- `composer dump-autoload`
- `composer require symfony/maker-bundle --dev`

