start:
	php artisan serve --host 127.0.0.1

setup:
	composer install
	php artisan migrate
	npm i
	npm run dev

watch:
	npm run watch

migrate:
	php artisan migrate

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	php artisan test

deploy:
	git push heroku

lint:
	composer phpcs

lint-fix:
	composer phpcbf
