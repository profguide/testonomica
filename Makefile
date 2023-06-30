# Install PHPSTORM plugin! Otherwise there is no way hot to see mistakes, that are much.
#SHELL := /bin/bash
#
#tests: export APP_ENV=test
#tests:
#	symfony console doctrine:database:drop --force || true
#	symfony console doctrine:database:create
#	symfony console doctrine:migrations:migrate -n
#	symfony console doctrine:fixtures:load -n
#	#symfony php bin/phpunit $@
#	php bin/phpunit
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
	sudo chown -R adavydov:adavydov var
	APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
#	echo "php bin/console cache:clear"
#	php bin/console cache:clear
	rm var/cache/ -r
	sudo chown -R www-data:www-data var

composer-install:
	APP_ENV=prod composer install --no-dev --optimize-autoloader
