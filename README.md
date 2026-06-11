# Crewfare — Entorno de prueba (live coding)

Stack listo para la entrevista técnica: **backend Laravel + MySQL** sirviendo un
API REST, y **frontend Vue 3 + TypeScript** que lo consume con **axios**. Todo
corre en Docker y se sirve bajo un único hostname: **http://crewfare.localhost**.

---

## 1. Requisitos

- Docker + Docker Compose (verificado con Docker 29 / Compose v5).
- Nada más: PHP, Composer y Node se ejecutan **dentro de los contenedores**.

## 2. Arranque rápido

> **Tras clonar en una máquina nueva:** no necesitas instalar nada a mano. Docker
> construye `vendor/` (Composer) y `node_modules/` (npm) dentro de las imágenes.

```bash
docker compose up -d --build          # levanta proxy + db + backend + frontend
docker compose exec backend php artisan migrate --seed   # crea tablas + datos
```

Luego abre en el navegador:

| URL                                   | Qué es                          |
|---------------------------------------|---------------------------------|
| http://crewfare.localhost             | Frontend Vue                    |
| http://crewfare.localhost/api/items   | Endpoint REST (JSON)            |
| http://crewfare.localhost/api/ping    | Healthcheck del API             |
| http://localhost:8080                 | Dashboard de Traefik (debug)    |

> Atajos en el `Makefile`: `make up`, `make migrate`, `make logs`, `make sh-back`, `make fresh`.

> ⚠️ **Puerto 80 ocupado:** tenías otro proyecto (`crm_nginx`) usando el puerto 80.
> Se detuvo para liberar `crewfare.localhost`. Para revivir el CRM más tarde:
> `docker start crm_nginx`. Para la entrevista, déjalo detenido.

### ¿`crewfare.localhost` no resuelve?

Chrome y Firefox modernos resuelven `*.localhost` a `127.0.0.1` automáticamente.
Si tu navegador no lo hace, añade esta línea a `/etc/hosts` (requiere sudo):

```
127.0.0.1   crewfare.localhost
```

## 3. Arquitectura

```
Navegador (host) ──HTTP──> crewfare.localhost  (Traefik :80)
        │                        │
        │   /api/* ──────────────┴──> backend (Laravel) ──Eloquent──> db (MySQL)
        │   /*  ─────────────────────> frontend (Vite/Vue)
        │
        └── axios GET /api/items  (mismo origen → SIN CORS)
```

| Servicio   | Imagen        | Puerto host         | Rol |
|------------|---------------|---------------------|-----|
| `proxy`    | `traefik:v3`  | 80 (+8080 dashboard)| Reverse proxy, enruta por path |
| `db`       | `mysql:8.0`   | — (interno)         | Persiste datos (volumen `db_data`) |
| `backend`  | `php:8.4-cli` | — (interno)         | API Laravel (`php artisan serve`) |
| `frontend` | `node:22`     | — (interno)         | Dev server de Vite |

- **Solo el proxy publica puertos.** Traefik enruta según `proxy/dynamic.yml`
  (provider de archivo): `/api/*` → backend, el resto → frontend.
- Al compartir el mismo origen, **no hay CORS**. El frontend llama a `/api` (ruta relativa).
- Entre contenedores, Laravel habla con MySQL por el host interno `db`.
- El HMR (hot reload) de Vite viaja por el proxy en el puerto 80.

### Estructura

```
crewfare/
├── docker-compose.yml      # 4 servicios (proxy, db, backend, frontend)
├── .env                    # variables compartidas (host, credenciales DB)
├── Makefile                # atajos
├── proxy/
│   └── dynamic.yml         # rutas de Traefik (file provider)
├── backend/                # Laravel 13
│   ├── Dockerfile
│   ├── routes/api.php      # rutas del API
│   ├── app/Models/Item.php
│   ├── app/Http/Controllers/ItemController.php
│   └── database/{migrations,seeders}/...
├── frontend/               # Vue 3 + Vite + TS
│   ├── Dockerfile
│   └── src/
│       ├── types.ts            # interface Item
│       ├── api/items.ts        # axios + funciones tipadas
│       └── components/ItemList.vue
└── docs/
    ├── guia-typescript.html    # cheatsheet TS + Vue (abrir en navegador)
    └── ejercicios.html         # ejercicios de práctica
```

## 4. El endpoint REST (lo que pide la prueba)

`routes/api.php` expone un recurso `items` (`apiResource`):

| Método | Ruta              | Acción           |
|--------|-------------------|------------------|
| GET    | `/api/items`      | lista            |
| POST   | `/api/items`      | crear            |
| GET    | `/api/items/{id}` | detalle          |
| PUT    | `/api/items/{id}` | actualizar       |
| DELETE | `/api/items/{id}` | borrar           |

El frontend (`src/api/items.ts`) consume `GET /api/items` con axios y lo pinta en
`src/components/ItemList.vue`.

## 5. Conexión a la base de datos (DBeaver / cliente externo)

| Campo    | Valor                     |
|----------|---------------------------|
| Host     | `127.0.0.1`               |
| Port     | `3306`                    |
| Database | `crewfare`                |
| User     | `crewfare` / pass `secret`|
| Root     | `root` / pass `root`      |

Si DBeaver da error de SSL/llave pública: en *Driver properties* pon
`allowPublicKeyRetrieval=true` y `useSSL=false`.

## 6. Comandos útiles

```bash
docker compose ps                    # estado de los servicios
docker compose logs -f backend       # logs del backend
docker compose exec backend bash     # shell en el backend (artisan, composer)
docker compose exec frontend sh      # shell en el frontend (npm)
docker compose down                  # parar (los datos persisten)
docker compose down -v               # parar y BORRAR la base de datos
```

## 7. Extensiones de VS Code (gratuitas, SIN IA)

**PHP / Laravel**
- **PHP Intelephense** (bmewburn) — IntelliSense de PHP (no es IA).
- **Laravel Extension Pack** (o por separado: Laravel Snippets, Blade, Artisan).
- **PHP Namespace Resolver** — importa/ordena los `use`.

**Vue / TS / JS**
- **Vue - Official (Volar)** — soporte oficial Vue 3 + TS. *Imprescindible.*
- **ESLint**, **Prettier**, **Error Lens** (errores inline).
- **Vue VSCode Snippets**, **JavaScript (ES6) code snippets**.

**Generales / Docker**
- **Docker** / **Container Tools** (Microsoft), **DotENV**, **EditorConfig**,
  **Path Intellisense**, **Auto Rename Tag**, **npm Intellisense**.

### ⚠️ Apagar la IA (lo pidieron explícitamente)

Desinstala o **deshabilita** antes de la entrevista: **GitHub Copilot, Codeium,
Tabnine, Cody, Amazon Q** y también **IntelliCode** de Microsoft (usa ML).

**¿Son mal vistas las demás extensiones?**
- *Language servers / linters / formatters* (Intelephense, Volar, ESLint, Prettier):
  **riesgo casi nulo**, son tooling profesional estándar. El autocompletado del
  lenguaje (no IA) es esperado.
- *Snippet packs*: **riesgo bajo**, pero apóyate poco en ellos para que se vea que
  conoces la sintaxis.
- *IA generativa*: prohibida aquí y mal vista en general. Déjala apagada y, si
  quieres, avísales: "tengo Copilot/IntelliCode deshabilitados".

## 8. Material de estudio

Abre en el navegador (doble click o `xdg-open`):

- `docs/guia-typescript.html` — funciones y bloques de código más frecuentes de
  TypeScript y Vue 3 Composition API.
- `docs/ejercicios.html` — serie de ejercicios progresivos.
