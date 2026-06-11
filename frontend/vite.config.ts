import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    // Escuchar en todas las interfaces para que Traefik (otro contenedor)
    // pueda alcanzar el dev server.
    host: true,
    port: 5173,
    strictPort: true,
    // Permitir el hostname que sirve el proxy.
    allowedHosts: ['crewfare.localhost'],
    // El HMR (hot reload) viaja por el proxy en el puerto 80, no el 5173.
    hmr: {
      clientPort: 80,
    },
  },
})
