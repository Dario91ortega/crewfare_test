# Atajos para el día de la entrevista.
.PHONY: up down build logs migrate seed fresh sh-back sh-front

up:        ## Levanta todo el stack
	docker compose up -d --build

down:      ## Apaga el stack (conserva los datos)
	docker compose down

build:     ## Reconstruye las imágenes
	docker compose build

logs:      ## Logs en vivo de todos los servicios
	docker compose logs -f

migrate:   ## Corre las migraciones + seeders
	docker compose exec backend php artisan migrate --seed

fresh:     ## Recrea la DB desde cero con datos de ejemplo
	docker compose exec backend php artisan migrate:fresh --seed

sh-back:   ## Shell dentro del contenedor backend
	docker compose exec backend bash

sh-front:  ## Shell dentro del contenedor frontend
	docker compose exec frontend sh
