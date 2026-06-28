# ShortLink — URL Shortener

A Laravel-based URL shortening application.

## Features

- Sign Up / Sign In / Logout (Laravel Auth)
- Dashboard with total URL count card
- Shorten URLs via Ajax (no page reload)
- Custom short code generation (no external API)
- URL validation on client and server
- Copy to clipboard button per row
- AdminLTE 3 template

## Tech Stack

- PHP 8.2 / Laravel 10
- MySQL
- AdminLTE 3
- jQuery (Ajax)

## Flow Explained

1. User registers or logs in via Laravel Auth
2. After login, redirected to `/` → `DashboardController` → shows total URL count
3. User visits `/urls` → `UrlController@index` → lists all their shortened URLs
4. User submits the form → Ajax POST to `/urls` → `UrlController@store`
   - Validates title and URL
   - Generates a unique 6-character short code
   - Saves to `urls` table
   - Returns JSON → new row prepended to table without page reload
5. User visits `/s/{code}` → `UrlController@redirect` → redirects to original URL
6. Copy button → copies short URL to clipboard via JavaScript

## Local Setup

```bash
git clone https://github.com/Dominic-K-Joseph/url-shortener.git
cd url-shortener
composer install
cp .env.example .env
php artisan key:generate
# set DB details in .env
php artisan migrate
php artisan serve
```

Visit http://127.0.0.1:8000

## Database Setup

Create a MySQL database before running migrations:

```sql
CREATE DATABASE db_url_shortener;
```

Update these values in your `.env` file:

## Future Enhancements

- Click count tracking per URL
- QR code generation
- Custom slug support
- URL expiry settings
- Admin panel for all users