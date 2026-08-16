.PHONY: up down check test

up:
	docker compose up -d --build
	@if [ ! -f .env ] || ! grep -q '^APP_KEY=.\+' .env; then \
		echo "Génération de la clé d'application manquante..."; \
		docker compose exec -T app php artisan key:generate; \
	fi

down:
	docker compose down -v

check:
	docker compose exec -T app composer check

test:
	docker compose exec -T app php artisan test
