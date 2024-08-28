#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install --no-interaction --optimize-autoloader
fi

if [ "$#" -gt 0 ]; then
  "$@"
fi

php-fpm