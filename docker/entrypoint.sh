#!/bin/sh
set -eu

cd /app

is_enabled() {
    case "${1:-false}" in
        1|true|TRUE|yes|YES|on|ON) return 0 ;;
        *) return 1 ;;
    esac
}

read_environment_value() {
    php -r '
        require "vendor/autoload.php";
        Dotenv\Dotenv::createImmutable(getcwd())->safeLoad();
        echo $_ENV[$argv[1]] ?? $argv[2];
    ' -- "$1" "$2"
}

wait_for_database() {
    timeout="${DB_WAIT_TIMEOUT:-$(read_environment_value DB_WAIT_TIMEOUT 60)}"
    elapsed=0

    until php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        Illuminate\Support\Facades\DB::connection()->getPdo();
    ' >/dev/null 2>&1; do
        if [ "$elapsed" -ge "$timeout" ]; then
            echo "Database tidak tersedia setelah ${timeout} detik." >&2
            exit 1
        fi

        elapsed=$((elapsed + 2))
        sleep 2
    done
}

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

role="${CONTAINER_ROLE:-$(read_environment_value CONTAINER_ROLE app)}"
app_environment="${APP_ENV:-$(read_environment_value APP_ENV production)}"
run_migrations="${RUN_MIGRATIONS:-$(read_environment_value RUN_MIGRATIONS true)}"
run_seeders="${RUN_SEEDERS:-$(read_environment_value RUN_SEEDERS false)}"

php artisan config:clear --no-interaction

if [ "$app_environment" = "production" ]; then
    php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        exit((string) config("app.key") === "" ? 1 : 0);
    ' || {
        echo "APP_KEY wajib diisi untuk production." >&2
        exit 1
    }
fi

wait_for_database

if [ "$role" = "app" ] || [ "$role" = "all" ]; then
    if is_enabled "$run_migrations"; then
        php artisan migrate --force --no-interaction
    fi

    if is_enabled "$run_seeders"; then
        php artisan db:seed --force --no-interaction
    fi

    if [ "$app_environment" != "production" ]; then
        php artisan optimize:clear --no-interaction
    fi
fi

if [ "$app_environment" = "production" ]; then
    php artisan config:cache --no-interaction
    php artisan event:cache --no-interaction

    if [ "$role" = "app" ] || [ "$role" = "all" ]; then
        php artisan storage:link --force --no-interaction
        php artisan view:cache --no-interaction
    fi
fi

if [ "${1:-}" = "app-runtime" ]; then
    if [ "$role" = "all" ]; then
        exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
    fi

    exec frankenphp run --config /etc/frankenphp/Caddyfile
fi

exec "$@"
