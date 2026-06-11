<script setup lang="ts">
import ItemList from './components/ItemList.vue'
</script>

<template>
  <main class="app">
    <h1>Crewfare · Vue 3 + TS</h1>
    <p class="muted">
      Frontend en <code>http://crewfare.localhost</code> consumiendo el API de
      Laravel en <code>/api/items</code> vía axios.
    </p>
    <ItemList />
  </main>
</template>

<style scoped>
.app {
  max-width: 640px;
  margin: 2rem auto;
  padding: 0 1rem;
  text-align: left;
}
.muted {
  color: #888;
}
code {
  background: #1f1f24;
  padding: 0.1rem 0.35rem;
  border-radius: 4px;
}
</style>
