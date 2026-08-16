.PHONY: up down check test

up:
	docker compose up -d --build

down:
	docker compose down -v

check:
	docker compose exec -T app composer check

test:
	docker compose exec -T app php artisan test
