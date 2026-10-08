## laraPMS project conventions

laraPMS is a multi-tenant property-management app (properties, rooms, customers, bookings) built with Laravel 13, Inertia 3 + Vue 3, Vite and Tailwind 3. Tests use Pest.

### Environment: always use DDEV
- Never run PHP, Composer or Node on the host. Prefix every command with `ddev`: `ddev artisan …`, `ddev composer …`, `ddev npm …`, `ddev php …`.
- Tests: `ddev artisan test` (Pest). App URL: `https://larapms.ddev.site`.
- The DDEV PHP version in `.ddev/config.yaml` must match the PHP version used in the `Dockerfile` (`serversideup/php:<version>-fpm-nginx`) and the `php` constraint in `composer.json`.

### Multi-tenancy
- Tenant-owned models use the `App\Traits\BelongsToTenant` trait: it adds `TenantScope` as a global scope and fills `tenant_id` from `session('active_tenant_id')` on create.
- The active tenant is stored on the user (`User::setActiveTenant()` / `getActiveTenant()`); the `Login` event listener `App\Listeners\SetInitialTenant` sets it after login.
- Authorization goes through policies in `app/Policies` (auto-discovered by naming convention: `Booking` → `BookingPolicy`). Always authorize tenant-owned records in controllers, and cover cross-tenant access in tests ("a user cannot access a record they do not own").

### Application structure (Laravel 11+ slim skeleton)
- There is no `Http/Kernel.php`, `Console/Kernel.php` or `Exceptions/Handler.php`. Middleware, routing, exceptions and trusted proxies are configured in `bootstrap/app.php`; service providers in `bootstrap/providers.php`.
- The post-login home route is the named route `dashboard`: use `route('dashboard', absolute: false)`.
- The app runs behind a reverse proxy (Dokploy/Traefik), so all proxies are trusted (`TrustedProxiesTest` covers this). Do not remove that configuration.

### Frontend
- Inertia pages live in `resources/js/Pages` (capital **P**), configured in `config/inertia.php`. Shared components are in `resources/js/Components`, layouts in `resources/js/Layouts`.
- Use Ziggy (`route()`) for named routes in Vue, and `useForm` from `@inertiajs/vue3` for forms.

### Deployment
- Production is a single container built from the root `Dockerfile` (nginx + PHP-FPM, port 8080), deployed with Dokploy. Migrations and Laravel caches run on container start.
- Do not add Laravel Cloud, Sail or SSH deploy configuration.

### Laravel Boost maintenance
- Add project guidelines to `.ai/guidelines/*.md`; never edit the generated block in `AGENTS.md` by hand.
- `php artisan boost:install` / `boost:update` rewrites `.mcp.json` to run `php artisan boost:mcp` on the host. This project has no host PHP, so after every run restore the DDEV command:
  ```json
  {
      "mcpServers": {
          "laravel-boost": {
              "command": "ddev",
              "args": ["php", "artisan", "boost:mcp"]
          }
      }
  }
  ```
- `boost:install` cannot pick an agent non-interactively; `boost.json` already lists `claude_code` and deliberately omits the Laravel Cloud skill (`"cloud": false`).
