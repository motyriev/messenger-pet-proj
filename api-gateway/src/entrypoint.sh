#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install --no-interaction --optimize-autoloader
fi

exec php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8080 --max-requests=1000
