COMPOSER = composer
PHPCS = vendor/bin/phpcs
PHPUNIT = vendor/bin/phpunit

PHPCS_FLAGS = --standard=PSR2 src tests

all: lint test

lint:
	$(PHPCS) $(PHPCS_FLAGS)

test:
	$(PHPUNIT)
