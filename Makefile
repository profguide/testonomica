# Install PHPSTORM plugin! Otherwise there is no way hot to see mistakes, that are much.
SHELL := /bin/bash

tests: export APP_ENV=test
tests:
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate -n
	symfony console doctrine:fixtures:load -n
	#symfony php bin/phpunit $@
	php bin/phpunit
.PHONY: tests