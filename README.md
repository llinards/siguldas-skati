# Siguldas Skati

A web application for managing and presenting rental properties (design houses, saunas, and hot tubs) in Sigulda,
Latvia. Built with Laravel 12, Livewire 3, and Tailwind CSS v4.

**Production:** [siguldasskati.lv](https://siguldasskati.lv)

## Tech Stack

- **Backend:** PHP 8.3, Laravel 12, Livewire 3
- **Frontend:** Tailwind CSS v4, Alpine.js v3, Vite 7
- **Database:** MySQL
- **Testing:** Pest 4
- **Localization:** Latvian (primary), English

## Requirements

- PHP 8.3+
- MySQL 8.0+
- Node.js 20+
- Composer 2

## Local Development Setup

### 1. Clone & Install Dependencies

```bash
git clone <repository-url>
cd siguldas-skati
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:

```
DB_DATABASE=siguldas_skati
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

The seeder creates an admin user, sample products with images, galleries, features, rules, and newsletter subscribers.

### 4. Storage Link

```bash
php artisan storage:link
```

### 5. Start Development Server

```bash
composer run dev
```

This starts the Laravel server, queue worker, log watcher, and Vite dev server concurrently.

Alternatively, if using [Laravel Herd](https://herd.laravel.com), the site is available at `http://siguldas-skati.test`.

## Project Structure

### Domain Models

| Model            | Description                                                    |
|------------------|----------------------------------------------------------------|
| **Product**      | Rental properties (translatable title, description, pricelist) |
| **ProductImage** | Product photo gallery                                          |
| **Feature**      | Product amenities with icons (translatable)                    |
| **Rule**         | Booking rules/conditions with icons (translatable)             |
| **Gallery**      | Photo galleries (translatable title)                           |
| **GalleryImage** | Gallery photos                                                 |
| **Newsletter**   | Newsletter subscribers                                         |

### Services Layer

Business logic is organized in `app/Services/`:

- `ProductServices` - Product CRUD & queries
- `GalleryServices` - Gallery CRUD & queries
- `FeatureService` / `RuleService` - Feature & rule operations
- `FileStorageService` - File upload handling
- `NewsletterService` - Newsletter subscriptions
- `FlashMessageService` - Session flash messages
- `ErrorLogService` - Error logging

### Livewire Components

**Public:** Newsletter subscription, Contact form

**Admin Dashboard (`/dashboard`):** Full CRUD for products, features, rules, galleries, and newsletter management with
drag-and-drop ordering.

## Testing

Run the full test suite:

```bash
php artisan test
```

Run specific tests:

```bash
php artisan test --compact --filter=ProductTest
```

Tests use `Storage::fake('public')` globally (configured in `tests/Pest.php`) to prevent writing files to disk.

## Code Style

Format PHP files with Laravel Pint:

```bash
vendor/bin/pint
```

Format frontend files with Prettier:

```bash
npm run format
```

## CI/CD

GitHub Actions workflow (`.github/workflows/tests-and-deployments.yml`):

- Runs tests on PHP 8.3, 8.4, and 8.5
- Triggers on pushes to `staging` and `master`, and on pull requests
- Deploys to staging/production via Laravel Forge

## Branching

- `master` - Production
- `staging` - Staging environment
- `development` - Active development
