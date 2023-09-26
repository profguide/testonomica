# run as root
# setup-files:
#	HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
#	setfacl -dR -m u:"$HTTPDUSER":rwX -m u:adavydov:rwX var
#	setfacl -R -m u:"$HTTPDUSER":rwX -m u:adavydov:rwX var

# Install PHPSTORM plugin! Otherwise there is no way hot to see mistakes, that are much.
#SHELL := /bin/bash
#
#tests: export APP_ENV=test
test:
	symfony console doctrine:database:drop --force --env=test || true
	symfony console doctrine:database:create --env=test
	symfony console doctrine:migrations:migrate -n  --env=test
	symfony console doctrine:fixtures:load -n  --env=test
	#symfony php bin/phpunit $@
	php bin/phpunit
#.PHONY: tests

dump-env:
	composer dump-env prod

# delete cache
# warmup cache
# chown var to www-data
clear-cache:
#	sudo rm var/cache -r
#	APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
#	sudo chown -R www-data:www-data var
#	sudo chown -R adavydov:adavydov var
	APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
#	echo "php bin/console cache:clear"
#	php bin/console cache:clear
#	rm var/cache/ -r
#	sudo chown -R www-data:www-data var

composer-install:
	APP_ENV=prod composer install --no-dev --optimize-autoloader
