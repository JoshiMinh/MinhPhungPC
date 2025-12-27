# MinhPhungPC → Laravel Migration Roadmap

This document captures the immediate errors observed in the current codebase and a phased plan to complete the migration from the legacy PHP app in `OLD/` to the Laravel application.

## Current Issues to Triage
- `php artisan test` currently fails because the repository includes an incomplete `vendor/` directory (`symfony/deprecation-contracts/function.php` is missing). Remove the tracked `vendor/` tree from version control and regenerate dependencies with `composer install` before any validation or CI.
- Required branding and component assets are absent (see `README.md`). Without restoring them under `public/` the UI will show broken images.
- No environment file is committed; copy `.env.example` to `.env` and supply DB/mail credentials before running migrations or seeds.

## Migration Goals
1. Reach feature parity for the storefront (builder, search, cart/checkout, account) and the admin console currently under `OLD/admin/`.
2. Ensure data compatibility so legacy customer accounts, carts, and orders migrate cleanly into the Laravel schema.
3. Establish reliable automated testing (PHPUnit) and deployment confidence for the new stack.

## Phased Plan
### 1) Foundation & Bootstrap
- Clean the working tree of committed vendor artifacts and run `composer install`, `npm install`, and `npm run build` (or `dev`) to unblock tests and local UI.
- Generate `.env`, set database/mail credentials, and run `php artisan key:generate`.
- Run `php artisan migrate` against a throwaway DB to validate migrations; create `php artisan storage:link`.

### 2) Schema & Data Migration
- Inventory legacy MySQL tables from `OLD/` scripts and align them with `database/migrations/*` (components, carts, orders, users). Add missing columns/indexes or new migrations as needed.
- Define ETL scripts or Laravel commands to pull legacy data, transform field names/types, and load into the new schema. Pay special attention to password hashing (convert legacy hashes or force resets).
- Seed representative catalog data (components by category) and demo accounts to mirror the legacy experience.

### 3) Feature Parity by Surface
- **Builder & Search:** Validate compatibility rules already coded in `BuildSetController` (socket type, DDR) against legacy behavior; add missing rules (PSU wattage, case form factor) before importing real data.
- **Cart & Checkout:** Mirror `OLD/_buildsetToCart.php` and order creation logic; ensure stock/availability checks exist; add graceful handling when components disappear.
- **Account/Auth:** Confirm registration/profile flows match legacy fields (DOB, address). Wire password reset/email verification flows to replace `OLD/_emailForgot.php`.
- **Admin:** Port `OLD/admin/*` (product CRUD, user/order management, dashboard) to Laravel resource controllers + Blade views with policies/guards.

### 4) UX & Assets
- Restore required images/icons to `public/` (and `public/component_icons/`) so builder cards and avatars render.
- Convert any remaining legacy PHP views to Blade templates; reuse existing layout/components to keep styling consistent.

### 5) Quality, Testing, and Release
- After dependencies are restored, re-enable `php artisan test`; add targeted feature tests for builder compatibility filters, cart totals, and auth flows.
- Add smoke checks for admin CRUD once ported.
- Stage a pilot deployment alongside the legacy site, migrate a snapshot of data, and perform manual UAT before full cutover. Maintain a rollback plan until parity is confirmed.
