# Taikon Motorparts

Online shop and workshop booking site for **Taikon Motorparts**, Kampung Batu 20, Kuang, Selangor.

- **Storefront:** categories, search, sorting and stock-aware product pages
- **Cart & checkout:** delivery or self-pickup, cash on delivery, bank transfer or pay at the workshop
- **Workshop booking:** live time-slot availability with per-slot capacity
- **Customer accounts:** order history, bookings (with self-cancel), saved address
- **Admin panel** (`/admin`): dashboard, products (with photo upload), categories, orders, bookings, services, customers and messages

Built with plain PHP 8.2+ and MySQL. There are no Composer or npm dependencies, so it runs on any standard PHP host, including XAMPP.

---

## Quick start (local)

Needs PHP 8.2+ (with `pdo_mysql`) and Docker. MySQL runs in Docker; PHP runs on your machine.

```bash
cp .env.example .env               # defaults already match docker-compose.yml
docker compose up -d               # MySQL 8.4 on localhost:3307; first start loads schema + sample catalogue
php bin/create-admin.php           # creates your admin login
php -S localhost:8000 -t public public/index.php
```

Open http://localhost:8000, then sign in and go to http://localhost:8000/admin.

### Database commands

| Task | Command |
|------|---------|
| Start / stop | `docker compose up -d` / `docker compose down` |
| MySQL shell | `docker exec -it taikon-db mysql -utaikon -ptaikon_secret taikon_motorparts` |
| Reset to the sample catalogue (keeps the container) | `php bin/install.php` |
| Wipe everything, including the data volume | `docker compose down -v`, then `docker compose up -d` |
| Back up | `docker exec taikon-db mysqldump -utaikon -ptaikon_secret taikon_motorparts > backup.sql` |

The schema and seed files only load automatically when the data volume is empty. After the first start, use `php bin/install.php` to reset.

### Without Docker (XAMPP or your own MySQL)

Set the `DB_*` values in `.env` to your server (XAMPP uses `DB_PORT=3306`, `DB_USERNAME=root` and an empty password). Then run `php bin/install.php` and `php bin/create-admin.php`. If the project sits in `htdocs/taikon_motorparts`, the root `.htaccess` routes requests through `public/`.

## Production checklist

- [ ] Point the web server's document root at **`/public`**
- [ ] `.env`: set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, `SESSION_SECURE=true`
- [ ] Change `DB_PASSWORD` and `DB_ROOT_PASSWORD` from the defaults before the database volume is first created (MySQL only reads them on first initialisation)
- [ ] Replace the placeholder phone/WhatsApp in `config/shop.php` and bank details in `config/shipping.php`
- [ ] Make `public/uploads/products` and `storage/logs` writable by the web server
- [ ] Review the sample prices in the admin panel and add product photos

## Project structure

```
app/
  Core/            Framework pieces: Router, Database (PDO), Session, Csrf, Auth, Validator, View
  Controllers/     Storefront controllers; Admin/ holds the admin panel
  Models/          One class per table, all queries use prepared statements
  Services/        Cart, OrderPlacer (transactional checkout), ImageUploader
  Support/         Paginator, BookingCalendar rules, SVG Icons
  helpers.php      Small view helpers: e(), url(), money(), icon(), old()…
  bootstrap.php    Autoloader, .env, config, session
bin/               CLI: install.php (reset DB), create-admin.php
docker-compose.yml MySQL 8.4 for local development
config/            app, database, shop, shipping, booking, catalog, uploads
database/          schema.sql, seed.sql
public/            Web root: index.php (front controller), assets/, uploads/
routes/web.php     Every URL in one file
storage/logs/      PHP error log
views/             layouts/, partials/, pages/, shop/, cart/, checkout/, auth/, account/, services/, admin/
_legacy/           The previous site, kept for reference only (safe to delete)
```

## Where to change things

| To change…                                   | Edit                                   |
|----------------------------------------------|----------------------------------------|
| Address, phone, hours, WhatsApp              | `config/shop.php`                      |
| Delivery fee, free-delivery threshold, bank  | `config/shipping.php`                  |
| Booking time slots, capacity, days ahead     | `config/booking.php`                   |
| Products per page, low-stock warning level   | `config/catalog.php`                   |
| Colours, fonts, spacing                      | `public/assets/css/base.css` (tokens)  |
| Products, categories, services, prices       | Admin panel                            |

## Security

- PDO prepared statements everywhere (no SQL injection)
- Passwords hashed with `password_hash()`, upgraded automatically when needed
- CSRF token on every POST
- Login throttling: 5 failed attempts per email/IP locks for 15 minutes
- Session ID is regenerated on login, and cookies are HttpOnly and SameSite=Lax
- Uploads: real MIME type is checked, files get random names, and scripts can't run in `uploads/`
- All output is escaped with `e()`
- Customers can only see their own orders and bookings
- Checkout locks stock rows so two buyers can't oversell the last unit

## Not included yet

- **Online card / FPX payment.** Orders support COD, bank transfer and pay-at-store. To add a gateway such as Billplz, ToyyibPay or Stripe, hook it in after `OrderPlacer::place()`.
- **Email notifications.** Confirmations are on-screen, and staff contact customers through the WhatsApp buttons in the admin panel.
- **Self-service password reset.** For now, an admin resets passwords with `bin/create-admin.php`, or the customer calls the shop.
