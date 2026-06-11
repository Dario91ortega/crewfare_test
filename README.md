# Crewfare — Test environment

A ready-to-code stack: a **Laravel + MySQL** backend serving a REST API, and a
**Vue 3 + TypeScript** frontend that consumes it with **axios**. Everything runs
in Docker and is served under a single hostname: **http://crewfare.localhost**.

---

## 1. Requirements

- Docker + Docker Compose (tested with Docker 29 / Compose v5).
- Nothing else: PHP, Composer and Node run **inside the containers**.

## 2. Quick start

> **After cloning on a fresh machine:** you don't need to install anything by hand.
> Docker builds `vendor/` (Composer) and `node_modules/` (npm) inside the images.

```bash
docker compose up -d --build          # starts proxy + db + backend + frontend
docker compose exec backend php artisan migrate --seed   # creates tables + data
```

Then open in the browser:

| URL                                   | What it is                  |
|---------------------------------------|-----------------------------|
| http://crewfare.localhost             | Vue frontend                |
| http://crewfare.localhost/api/items   | REST endpoint (JSON)        |
| http://crewfare.localhost/api/ping    | API healthcheck             |
| http://localhost:8080                 | Traefik dashboard (debug)   |

> `Makefile` shortcuts: `make up`, `make migrate`, `make logs`, `make sh-back`, `make fresh`.

### `crewfare.localhost` doesn't resolve?

Modern Chrome and Firefox resolve `*.localhost` to `127.0.0.1` automatically. If
your browser doesn't, add this line to `/etc/hosts` (requires sudo):

```
127.0.0.1   crewfare.localhost
```

## 3. Architecture

```
Browser (host) ──HTTP──> crewfare.localhost  (Traefik :80)
        │                      │
        │   /api/* ────────────┴──> backend (Laravel) ──Eloquent──> db (MySQL)
        │   /*  ───────────────────> frontend (Vite/Vue)
        │
        └── axios GET /api/items  (same origin → NO CORS)
```

| Service    | Image         | Host port           | Role |
|------------|---------------|---------------------|------|
| `proxy`    | `traefik:v3`  | 80 (+8080 dashboard)| Reverse proxy, routes by path |
| `db`       | `mysql:8.0`   | 3306                | Persists data (volume `db_data`) |
| `backend`  | `php:8.4-cli` | — (internal)        | Laravel API (`php artisan serve`) |
| `frontend` | `node:22`     | — (internal)        | Vite dev server |

- **Only the proxy and db publish ports.** Traefik routes based on
  `proxy/dynamic.yml` (file provider): `/api/*` → backend, everything else → frontend.
- Sharing the same origin means **no CORS**. The frontend calls `/api` (relative path).
- Between containers, Laravel talks to MySQL via the internal host `db`.
- Vite's HMR (hot reload) travels through the proxy on port 80.

### Structure

```
crewfare/
├── docker-compose.yml      # 4 services (proxy, db, backend, frontend)
├── .env                    # shared variables (host, DB credentials)
├── Makefile                # shortcuts
├── proxy/
│   └── dynamic.yml         # Traefik routes (file provider)
├── backend/                # Laravel 13
│   ├── Dockerfile
│   ├── routes/api.php      # API routes
│   ├── app/Models/Item.php
│   ├── app/Http/Controllers/ItemController.php
│   └── database/{migrations,seeders}/...
└── frontend/               # Vue 3 + Vite + TS
    ├── Dockerfile
    └── src/
        ├── types.ts            # interface Item
        ├── api/items.ts        # axios + typed functions
        └── components/ItemList.vue
```

## 4. The REST endpoint

`routes/api.php` exposes an `items` resource (`apiResource`):

| Method | Path              | Action     |
|--------|-------------------|------------|
| GET    | `/api/items`      | list       |
| POST   | `/api/items`      | create     |
| GET    | `/api/items/{id}` | show       |
| PUT    | `/api/items/{id}` | update     |
| DELETE | `/api/items/{id}` | delete     |

The frontend (`src/api/items.ts`) consumes `GET /api/items` with axios and renders
it in `src/components/ItemList.vue`.

## 5. Database connection (DBeaver / external client)

| Field    | Value                     |
|----------|---------------------------|
| Host     | `127.0.0.1`               |
| Port     | `3306`                    |
| Database | `crewfare`                |
| User     | `crewfare` / pass `secret`|
| Root     | `root` / pass `root`      |

If DBeaver throws an SSL / public-key error: under *Driver properties* set
`allowPublicKeyRetrieval=true` and `useSSL=false`.

## 6. Useful commands

```bash
docker compose ps                    # service status
docker compose logs -f backend       # backend logs
docker compose exec backend bash     # shell into the backend (artisan, composer)
docker compose exec frontend sh      # shell into the frontend (npm)
docker compose down                  # stop (data persists)
docker compose down -v               # stop and DROP the database
```
