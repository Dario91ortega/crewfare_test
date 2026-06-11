<script setup lang="ts">
// Componente GENÉRICO de consumo de API.
// No conoce ningún formato concreto: pega cualquier URL, hace GET y muestra
// la respuesta cruda tal cual, sea cual sea su estructura.
import { ref, onMounted } from 'vue'
import { http } from '../api/http'

// URL a consultar. Escribe lo que sea:
//  - URL externa:  https://rickandmortyapi.com/api/character  -> pasa por el backend (sin CORS)
//  - ruta local:   /api/items                                 -> se llama directa
const url = ref('https://rickandmortyapi.com/api/character')

// 'unknown' a propósito: no asumimos NADA sobre la forma de la respuesta.
const data = ref<unknown>(null)
const loading = ref(false)
const error = ref<string | null>(null)

async function load(): Promise<void> {
  loading.value = true
  error.value = null
  data.value = null
  try {
    const target = url.value.trim()
    const isExternal = /^https?:\/\//i.test(target)

    // Si es externa, la enrutamos por el proxy del backend (/api/proxy?url=...)
    // para esquivar CORS. Si es relativa, la pedimos directa (mismo origen).
    data.value = isExternal
      ? await http.get('/api/proxy', { params: { url: target } })
      : await http.get(target)
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Error desconocido'
  } finally {
    loading.value = false
  }
}

// Carga la URL por defecto al montar.
onMounted(load)
</script>

<template>
  <section>
    <header class="bar">
      <h2>Visor de API (genérico)</h2>
    </header>

    <form class="row" @submit.prevent="load">
      <input v-model="url" type="text" placeholder="URL del API…" />
      <button :disabled="loading" type="submit">
        {{ loading ? 'Cargando…' : 'GET' }}
      </button>
    </form>

    <p v-if="loading" class="muted">Cargando…</p>
    <p v-else-if="error" class="error">⚠️ {{ error }}</p>

    <!-- JSON crudo formateado: JSON.stringify(valor, null, 2) indenta con 2 espacios. -->
    <pre v-else-if="data !== null" class="result">{{ JSON.stringify(data, null, 2) }}</pre>
  </section>
</template>

<style scoped>
.bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.row {
  display: flex;
  gap: 0.5rem;
  margin: 0.75rem 0;
}
.row input {
  flex: 1;
  padding: 0.4rem 0.6rem;
  border: 1px solid #2c2c34;
  border-radius: 6px;
  background: #1f1f24;
  color: inherit;
}
.result {
  border: 1px solid #2c2c34;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  text-align: left;
  overflow-x: auto;
  font-family: monospace;
  white-space: pre-wrap;
  word-break: break-word;
}
.muted {
  color: #888;
}
.error {
  color: #e05252;
}
button {
  cursor: pointer;
}
</style>
