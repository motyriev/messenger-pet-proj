FROM ghcr.io/roadrunner-server/roadrunner:2024.2.0 AS roadrunner
FROM motyriev/php-grpc-protobuf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin


WORKDIR /var/www
COPY /src /var/www

RUN protoc --php_out=./app/Grpc --grpc_out=./app/Grpc --plugin=protoc-gen-grpc=/usr/local/bin/grpc/cmake/build/grpc_php_plugin resources/proto/friends.proto


COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN usermod -u 1000 www-data

RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www

USER www-data

ENTRYPOINT ["/bin/bash", "/var/www/entrypoint.sh"]
