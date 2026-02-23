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

<!-- Note: 2026-01-18 - Production environment variables verified -->

<!-- Note: 2026-01-21 - Production environment variables verified -->

<!-- Note: 2026-02-16 - Production environment variables verified -->

<!-- Note: 2026-02-23 - Production environment variables verified -->
