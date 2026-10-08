# Accounting System

Invoicing, payment tracking, expenses, purchasing, and profit-loss reporting for small
businesses. Built with Laravel 12, PHP 8.2, and Blade.

The interface and seed data are in Nepali (`ne`), with English available via the
language switcher.

## Features

| Module          | What it covers                                                   |
| --------------- | ---------------------------------------------------------------- |
| Dashboard       | Revenue, receivables, expenses, monthly totals, recent activity  |
| Products        | Item catalogue with SKU, price, and stock                        |
| Clients         | Customer records including PAN and contact details               |
| Suppliers       | Vendor records for payables                                      |
| Invoices        | Line items, discount, tax, status tracking, printable view       |
| Payments        | Full and partial payments against invoices, paid/due balances    |
| Expenses        | Categorised expense tracking by date                             |
| Purchase Orders | Orders raised to suppliers with line items                       |
| Ledger          | Combined transaction history of invoices, payments, and expenses |
| Reports         | Report index and profit & loss statement                         |
| Administration  | Users, roles, and permission assignment                          |

### Invoicing rules

- Invoice numbers are generated as `INV-YYYYMMDD-NNNN`, incrementing per day.
- Tax is one of `none`, `vat`, or `service`; it is applied after the discount.
- An invoice is `paid`, `partial`, or `unpaid`, derived from the paid amount against
  the total.
- Invoices past their `due_date` while still unpaid are treated as overdue.

## Requirements

- PHP 8.2 or newer
- Composer 2
- Node.js 20 or newer
- SQLite (default for local development) or MySQL 8

## Installation

```bash
composer run setup
```

That installs dependencies, creates `.env` from `.env.example`, generates the app key,
runs migrations, and builds the front end. For SQLite, create the database file first:

```bash
touch database/database.sqlite
```

To seed roles, permissions, and starter accounts:

```bash
php artisan db:seed
```

## Seeded accounts

| Role                       | Email                  | Password         |
| -------------------------- | ---------------------- | ---------------- |
| सुपर प्रशासक (super admin) | `admin@gmail.com`      | `Admin@123`      |
| प्रशासक (admin)            | `manager@gmail.com`    | `Manager@123`    |
| लेखापाल (accountant)       | `accountant@gmail.com` | `Accountant@123` |
| लेखापरीक्षक (auditor)      | `auditor@gmail.com`    | `Auditor@123`    |

> Change these before deploying anywhere. They exist for local development only.

## Development

```bash
composer run dev
```

This starts the PHP server, the queue worker, and the Vite dev server together.

Other useful commands:

```bash
composer run test      # run the Pest test suite
vendor/bin/pint        # format PHP code
```

## Access control

Authentication is handled by Laravel's session guard. On top of that there are two
middleware layers:

- `role:super-admin,admin` gates the whole `/accounting` area.
- `permission:users.manage` and `permission:roles.manage` gate user and role
  administration.

Permissions available for assignment:

`products.manage`, `clients.manage`, `suppliers.manage`, `invoices.manage`,
`payments.manage`, `expenses.manage`, `purchase-orders.manage`, `reports.view`,
`users.manage`, `roles.manage`.

## Project layout

```
app/Http/Controllers/Accounting/   Accounting module controllers
app/Http/Middleware/               Role, permission, and locale middleware
app/Models/                        Eloquent models
resources/views/accounting/        Blade views for the accounting module
resources/views/dashboard/pages/   User and role administration views
routes/web.php                     Route definitions
tests/Feature/                     Pest feature tests
```

## Notes

- This project was converted from an earlier news portal. The folder and GitHub
  repository are still named `accounting-system`, which is a leftover and no longer reflects
  what the software does. There is no AI functionality in the codebase.
- `2026_06_14_000010_remove_news_features.php` drops the tables from that earlier
  version. It has no `down()` implementation, so rolling it back is not supported.

## License

MIT
