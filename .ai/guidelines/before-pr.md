## Before opening a PR

Run these checks locally and fix everything they report before pushing:
- `ddev artisan test --compact`
- `ddev exec vendor/bin/pint --test` (fix with `ddev exec vendor/bin/pint`)
- `ddev npm run build`, so Vue and import errors fail here and not in production
