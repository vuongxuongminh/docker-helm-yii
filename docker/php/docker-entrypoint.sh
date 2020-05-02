#!/bin/sh
set -e

if [ "$1" = 'fpm' ] || [ "$1" = 'supervisor' ] || [ "$1" = 'setup' ]; then
  mkdir -p \
        /yii/web/assets \
        /yii/runtime/logs \
        /yii/runtime/cache;
  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /yii/runtime
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /yii/runtime
  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /yii/web/assets
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /yii/web/assets

	if [ "$1" = 'supervisor' ]; then
	  cp /var/supervisord/base.conf /var/supervisord/supervisord.conf

    { \
      echo '[inet_http_server]'; \
      echo 'port = *:9000'; \
      echo "username = ${SUPERVISOR_USERNAME:-root}"; \
      echo "password = ${SUPERVISOR_PASSWORD:-root}"; \
    } >> /var/supervisord/supervisord.conf

    set -- supervisord -c /var/supervisord/supervisord.conf

    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /var/supervisord
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /var/supervisord
  elif [ "$1" = 'fpm' ]; then
    set -- php-fpm
  elif [ "$1" = 'setup' ]; then
    # Install composer package & run migrate on dev env.

    composer install --prefer-dist --no-progress --no-suggest --no-interaction

    echo "Waiting for db to be ready..."

    until ./yii migrate/history > /dev/null 2>&1; do
      sleep 1
    done

    if ls -A migrations/*.php > /dev/null 2>&1; then
      ./yii migrate/up --interactive 0
    fi

    exit 0
	fi

fi

exec docker-php-entrypoint "$@"