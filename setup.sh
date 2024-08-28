#!/bin/bash

run_php_seeds() {
  local container_name=$1

  echo "Running migrations and seeds for PHP microservice ($container_name)"

  if docker exec -it $container_name php artisan migrate:refresh --force; then
    echo "Migrations for $container_name completed"
  else
    echo "Migrations for $container_name failed" >&2
    exit 1
  fi

  if docker exec -it $container_name php artisan db:seed --force; then
    echo "Seeds for $container_name completed"
  else
    echo "Seeds for $container_name failed" >&2
    exit 1
  fi
}

run_php_tests() {
  local container_name=$1

  echo "Running tests for $container_name"

  if docker exec -it $container_name php artisan test; then
    echo "Tests for $container_name passed"
  else
    echo "Tests for $container_name failed" >&2
    exit 1
  fi
}

run_go_seeds() {
  local container_name=$1

  echo "Running seeds for go microservice ($container_name)"

  docker exec -it friends-app ./main seed

  echo "Seeds for go microservice ($container_name) completed"
}


run_php_seeds "api_gateway-app"
run_php_seeds "chat-app"
run_go_seeds "friends-app"

echo "All seeds are completed"

read -p "do you want to run tests? (y/n): " answer
if [ "$answer" == "y" ]; then
  run_php_tests "api_gateway-app"
  run_php_tests "chat-app"
fi