COMPOSER = composer
PHPCS = vendor/bin/phpcs
PHPUNIT = vendor/bin/phpunit

PHPCS_FLAGS = --standard=PSR2 src tests
PHPUNIT_FLAGS =
PHPUNIT_COVERAGE_FLAGS = --coverage-html=coverage

all: lint test

lint: force
	$(PHPCS) $(PHPCS_FLAGS)

test: force
	$(PHPUNIT) $(PHPUNIT_FLAGS)

coverage: force
	$(PHPUNIT) $(PHPUNIT_COVERAGE_FLAGS)

force:
