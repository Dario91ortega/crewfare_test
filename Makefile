# Atajos para el día de la entrevista.
.PHONY: up down build logs migrate seed fresh sh-back sh-front \
        art controller model migration seeder request resource crud \
        routes tinker optimize dump

# Alias corto para artisan dentro del contenedor.
ARTISAN = docker compose exec backend php artisan

up:        ## Levanta todo el stack
	docker compose up -d --build

down:      ## Apaga el stack (conserva los datos)
	docker compose down

build:     ## Reconstruye las imágenes
	docker compose build

logs:      ## Logs en vivo de todos los servicios
	docker compose logs -f

migrate:   ## Corre las migraciones + seeders
	$(ARTISAN) migrate --seed

fresh:     ## Recrea la DB desde cero con datos de ejemplo
	$(ARTISAN) migrate:fresh --seed

sh-back:   ## Shell dentro del contenedor backend
	docker compose exec backend bash

sh-front:  ## Shell dentro del contenedor frontend
	docker compose exec frontend sh

# ---------------------------------------------------------------------------
# Generadores de Laravel (artisan make:*). Se pasan con name=...
#   Ej:  make controller name=PostController
#        make model name=Post
#        make migration name=create_posts_table
#        make crud name=Post     (modelo + migración + controller API + resource)
#
# Comando completo equivalente de cada atajo (por si lo quieres correr a mano):
#   controller: docker compose exec backend php artisan make:controller PostController
#   model:      docker compose exec backend php artisan make:model Post
#   migration:  docker compose exec backend php artisan make:migration create_posts_table
#   seeder:     docker compose exec backend php artisan make:seeder PostSeeder
#   request:    docker compose exec backend php artisan make:request StorePostRequest
#   resource:   docker compose exec backend php artisan make:resource PostResource
#   crud:       docker compose exec backend php artisan make:model Post -m
#               docker compose exec backend php artisan make:controller PostController --api --model=Post --requests
#               docker compose exec backend php artisan make:resource PostResource
#               (y luego registra a mano la ruta en routes/api.php)
#   routes:     docker compose exec backend php artisan route:list
#   tinker:     docker compose exec backend php artisan tinker
#   optimize:   docker compose exec backend php artisan optimize:clear
#   dump:       docker compose exec backend composer dump-autoload --optimize
#   art:        docker compose exec backend php artisan <lo-que-sea>
# ---------------------------------------------------------------------------

art:        ## Comando artisan libre:  make art cmd="route:list --json"
	$(ARTISAN) $(cmd)

controller: ## Crea un controller:  make controller name=PostController
	$(ARTISAN) make:controller $(name)

model:      ## Crea un modelo:  make model name=Post  (añade flags en name, ej. "Post -m")
	$(ARTISAN) make:model $(name)

migration:  ## Crea una migración:  make migration name=create_posts_table
	$(ARTISAN) make:migration $(name)

seeder:     ## Crea un seeder:  make seeder name=PostSeeder
	$(ARTISAN) make:seeder $(name)

request:    ## Crea un Form Request (validación):  make request name=StorePostRequest
	$(ARTISAN) make:request $(name)

resource:   ## Crea un API Resource (transforma la respuesta JSON):  make resource name=PostResource
	$(ARTISAN) make:resource $(name)

crud:       ## Modelo + migración + controller API completo (con FormRequests + model binding) + resource:  make crud name=Post
	$(ARTISAN) make:model $(name) -m
	$(ARTISAN) make:controller $(name)Controller --api --model=$(name) --requests
	$(ARTISAN) make:resource $(name)Resource
	@echo ""
	@echo ">> Falta registrar la ruta a mano en routes/api.php:"
	@echo "   Route::apiResource('$(shell echo $(name) | tr A-Z a-z)s', \\App\\Http\\Controllers\\$(name)Controller::class);"

routes:     ## Lista todas las rutas registradas
	$(ARTISAN) route:list

tinker:     ## REPL interactivo de Laravel (probar Eloquent en vivo)
	$(ARTISAN) tinker

optimize:   ## Limpia cachés de config/rutas (útil si algo "no se actualiza")
	$(ARTISAN) optimize:clear

dump:       ## Regenera el classmap (necesario para que tinker aliasee modelos nuevos por nombre corto)
	docker compose exec backend composer dump-autoload --optimize
