# Imagen ligera de Node 22 (LTS) para el dev server de Vite.
FROM node:22-alpine

WORKDIR /app

# Instalamos dependencias primero para aprovechar la cache de Docker.
COPY package*.json ./
RUN npm install

# El resto del código se monta como volumen desde ./frontend
# (ver docker-compose.yml), así que el contenedor usa el código vivo del host.

EXPOSE 5173

# Dev server accesible desde otros contenedores (Traefik) -> --host.
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
