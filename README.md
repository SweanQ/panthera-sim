# Panthera

Panthera is a custom-apparel storefront and production management app built with Laravel 12.

## Features

- Search, filter, and sort the public product catalog.
- Submit a custom apparel order with an on-page price estimate.
- Send a consultation request for special designs or bulk orders.
- Track a submitted order with its code and the WhatsApp number used at checkout.
- Review consultation requests and update their status in the staff portal.
- Manage production status, payment records, and invoices in the existing operations portal.
- Let admins and employees cancel orders before production starts, with a required reason and cancellation audit details.
- Require admins to enter an order code before permanently deleting an order and its payment history.
- Give employees a dedicated workspace for production orders, apparel variants, raw-material stock, and catalog updates.
- Record every apparel/material stock movement with its quantity, reason, before/after balance, and employee.
- Show low-stock indicators using configurable minimum quantities.
- Let admins and employees archive/restore catalog products; permanent deletion is restricted to products without orders or stock variants.
- Let admins and employees upload product photos for the staff and public catalogs.

Public orders start in the consultation stage. The displayed total is an estimate; staff must confirm the specifications and final price before production or payment. Registered customers can view the orders they place while signed in in their account history. Guest orders remain available through code-and-WhatsApp tracking.

Cancellation preserves the order and payment records; it does not issue a refund automatically. Admins can permanently delete an order only after confirming its order code.

Employees sign in through the shared login page and are redirected to `/karyawan`. Apparel stock is tracked per product, size, and color; raw materials include a unit, minimum quantity, optional unit cost, and notes. Stock changes are manual and auditable—creating or updating an order does not automatically consume apparel or material stock.

## Local setup

1. Install PHP dependencies and create the environment file:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

2. Start MySQL and create the database named `panthera_db`, or update the database values in `.env` to match your local setup.
3. Run migrations and load sample products:

   ```powershell
   php artisan migrate --seed
   ```

   Expose uploaded catalog photos through the public storage link:

   ```powershell
   php artisan storage:link
   ```

4. Install frontend dependencies:

   ```powershell
   npm install
   ```

5. In separate terminals, start Laravel and Vite:

   ```powershell
   php artisan serve
   ```

   ```powershell
   npm run dev
   ```

Open `http://127.0.0.1:8000` to view the storefront.

The local seed includes `admin@panthera.com` and `pegawai@panthera.com`, both with the development password `password123`. Change these credentials before deployment.

## Tests

```powershell
php artisan test
```
