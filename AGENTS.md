<INSTRUCTIONS>
## Project overview
- Framework: Laravel 10 (PHP 8.1), Blade views, Vite build pipeline.
- Frontend: Tailwind via Vite, plus custom CSS files served from `public/css` and `public/landing/assets`.
- Realtime: Pusher/Laravel Echo (see `package.json` and `composer.json`).

## Key locations
- Routes: `routes/web.php`, `routes/admin.php`, `routes/teacher.php`, `routes/student.php`.
- Controllers: `app/Http/Controllers`.
- Models: `app/Models`.
- Views (Blade): `resources/views`.
- Source assets: `resources/css/app.css`, `resources/js/app.js`.
- Built assets: `public/build` (do not edit by hand).
- Page-specific CSS: `public/css/*.css` and `public/landing/assets/css/*.css`.

## Workflow
- Install: `composer install` and `npm install` (or `composer setup` if you want the full bootstrap).
- Dev server (PHP + queue + Vite): `composer dev`.
- Build assets: `npm run build`.
- Tests: `composer test` or `php artisan test`.
- Format (PHP): `./vendor/bin/pint`.

## Conventions
- Prefer editing Blade templates in `resources/views`.
- Check the Blade template to see whether a page links to `public/css/*` or Vite assets before moving styles.
- Avoid editing `public/build` artifacts; regenerate via Vite instead.
</INSTRUCTIONS>
