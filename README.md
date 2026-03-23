# MinhPhungPC

Dynamic PC building and component management web application.

## Structure
- `/app`: Application root (pages, admin, lib, components)
- `/storage`: Media and user-uploaded content
- `/scripts`, `/styles`: Consolidated assets
- `schema.sql`: Database definition
- `.env`: Environment configuration

## Setup
1. Clone the repository.
2. Import `schema.sql` into MySQL.
3. Configure `.env` with your DB and SMTP credentials.
4. Host with a PHP-compatible server (e.g., Apache/XAMPP).
5. Access via `/app/index.php`.

## Features
- PC Build customizer
- Categorized component browsing
- User authentication and order history
- Admin dashboard for product/user management
- Dark mode and responsive design