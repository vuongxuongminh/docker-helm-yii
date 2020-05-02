ARG PHP_VERSION='7.4.5'
ARG NGINX_VERSION='1.17'

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS php

ARG YII_ENV
ARG YII_DEBUG

# extensions default version
ARG APCU_VERSION=5.1.18
ARG AMQP_VERSION=1.9.4
ARG IMAGICK_VERSION=3.4.4

RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		rabbitmq-c-dev \
		imagemagick-dev \
		libtool \
	; \
	apk add --no-cache python3; \
	pip3 install supervisor; \
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		pdo_mysql \
		sockets \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		amqp-${AMQP_VERSION} \
		imagick-${IMAGICK_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		amqp \
		imagick \
	; \
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	apk add --no-cache --virtual .imagick-runtime-deps imagemagick; \
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chmod +x /usr/bin/composer; sync

RUN if [ "${YII_ENV}" = 'prod' ]; then \
    ln -sf $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini; \
    else \
    ln -sf $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini; \
    fi

COPY /docker/php/conf.d/$YII_ENV.ini $PHP_INI_DIR/conf.d/config.ini
COPY /docker/php/supervisord /var/supervisord

WORKDIR /yii

# Copy needed resources of app.
COPY /yii/assets /yii/assets
COPY /yii/commands /yii/commands
COPY /yii/config /yii/config
COPY /yii/controllers /yii/controllers
COPY /yii/mail /yii/mail
COPY /yii/models /yii/models
COPY /yii/views /yii/views
COPY /yii/web /yii/web
COPY /yii/widgets /yii/widgets
COPY /yii/yii /yii/yii
COPY /yii/.env /yii/.env
COPY /yii/composer.json /yii/composer.json
COPY /yii/composer.lock /yii/composer.lock

RUN mkdir -p /yii/runtime/logs /yii/runtime/cache

ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN if [ "${YII_ENV}" = 'prod' ]; then \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	else \
    composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
    fi

RUN chmod +x yii; sync

RUN	if [ "$YII_ENV" = 'prod' ]; then \
	composer dump-autoload --classmap-authoritative --no-dev; \
	rm -rf /yii/web/assets; \
	mkdir -p /yii/web/assets; \
    ./yii staticAssets/asset/publish /yii/web/assets; \
	else \
	composer dump-autoload --classmap-authoritative; \
	fi

VOLUME /yii/runtime

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

COPY /docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY /docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck

RUN chmod +x /usr/local/bin/docker-entrypoint /usr/local/bin/docker-healthcheck

ENTRYPOINT ["docker-entrypoint"]

# "nginx" stage
FROM nginx:${NGINX_VERSION}-alpine AS nginx

# Add curl for health check.
RUN set -eux; \
    apk add curl --no-cache

WORKDIR /yii/web

COPY /docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf.stuff
COPY /docker/nginx/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY /docker/nginx/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
COPY --from=php /yii/web /yii/web

RUN chmod +x /usr/local/bin/docker-entrypoint /usr/local/bin/docker-healthcheck

ENTRYPOINT ["docker-entrypoint"]
CMD ["nginx", "-g", "daemon off;"]