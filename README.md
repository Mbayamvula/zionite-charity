# Zionite Charity

Humanitarian charity website built with PHP and MySQL.

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache with `mod_rewrite` (optional)

## Local setup (Laragon)

1. Clone this repository into `www/`
2. Copy `includes/env.example.php` to `includes/env.php`
3. Import `database/schema.sql` into MySQL
4. Open `http://localhost/Zionite%20charity`

Default admin (after schema import): see `database/schema.sql` — change password immediately via `reset_admin.php` on localhost.

## Production deployment

See [DEPLOY.txt](DEPLOY.txt).

## Configuration

All secrets and environment settings live in `includes/env.php` (not in Git). Use `includes/env.example.php` as a template.

## Security

- Zionite AI Security Agent (threat detection)
- CSRF protection on forms
- Stripe / PayPal / bank transfer / cash donations
