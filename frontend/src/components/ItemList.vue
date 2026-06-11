<script setup lang="ts">
// Composition API con TypeScript: <script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Item } from '../types'
import { getItems } from '../api/items'

// Estado reactivo tipado.
const items = ref<Item[]>([])
const loading = ref<boolean>(false)
const error = ref<string | null>(null)

// Carga los items desde el API.
async function load(): Promise<void> {
  loading.value = true
  error.value = null
  try {
    items.value = await getItems()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Error desconocido'
  } finally {
    loading.value = false
  }
}

function helloworld(text:string) {

  console.log(text);

  for (let i = 1; i <= [1,2].length; i++) {
    console.log ('for'+i);    
  }

  return text+' Daro hello';
}

// Se ejecuta cuando el componente se monta en pantalla.
onMounted(load)
</script>

<template>
  <section>
    <header class="bar">
      <h2>Items</h2>
      <button :disabled="loading" @click="load">
        {{ loading ? 'Cargando…' : 'Recargar' }}
      </button>
    </header>

    <div>
      {{ helloworld('String param1') }}
    </div>

    <p v-if="loading" class="muted">Cargando datos del API…</p>
    <p v-else-if="error" class="error">⚠️ {{ error }}</p>
    <p v-else-if="items.length === 0" class="muted">No hay items.</p>

    <ul v-else class="list">
      <li v-for="item in items" :key="item.id" class="card">
        <div class="card-head">
          <strong>{{ item.name }}</strong>
          <span class="price">${{ item.price.toFixed(2) }}</span>
        </div>
        <p class="muted">{{ item.description }}</p>
      </li>
    </ul>
  </section>
</template>

<style scoped>
.bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.list {
  list-style: none;
  padding: 0;
  display: grid;
  gap: 0.75rem;
}
.card {
  border: 1px solid #2c2c34;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  text-align: left;
}
.card-head {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
.price {
  color: #42b883;
  font-variant-numeric: tabular-nums;
}
.muted {
  color: #888;
  margin: 0.25rem 0 0;
}
.error {
  color: #e05252;
}
button {
  cursor: pointer;
}
</style>
