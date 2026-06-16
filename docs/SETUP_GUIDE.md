# Local Development & Environment Setup

### Requirements
- PHP >= 8.2 with BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML
- Composer 2.x
- Node.js & NPM
- MySQL / MariaDB

### Installation Steps
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
```
