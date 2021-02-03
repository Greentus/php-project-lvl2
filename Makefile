install:
	composer install

diff:
	./bin/gendiff

validate:
	composer validate

autoload:
	composer -o dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src bin
stan:
	composer exec --verbose phpstan -- --level=8 analyse src bin

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
