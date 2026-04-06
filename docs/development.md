# Publishing Views

To customise component templates:

```bash
php artisan vendor:publish --tag="tallui-views"
```

Views are published to `resources/views/vendor/tallui/`. Edited files take precedence over the package defaults.

---

## Local Development (Workbench)

The package ships with a workbench demo app powered by [Orchestra Testbench](https://packages.tools/testbench).

```bash
# Discover package, run migrations, seed demo data, build assets
composer build

# Start the local dev server at http://localhost:8000
composer start
```

`composer start` is equivalent to:

```bash
vendor/bin/testbench workbench:build --ansi
vendor/bin/testbench serve
```

To seed the database independently:

```bash
vendor/bin/testbench migrate:fresh --seed
```

---

## Testing

```bash
# Fix code style with Pint
composer lint

# Check code style without fixing
composer test:lint

# Apply Rector refactors
composer refacto

# Dry-run Rector (check only)
composer test:refacto

# Static analysis with PHPStan/Larastan
composer test:types

# Run unit & feature tests with Pest (parallel)
composer test:unit

# Full suite: refacto check + lint check + types + tests
composer test

# Tests with code coverage report
composer test-coverage
```

---

← [Back to docs](../README.md)
