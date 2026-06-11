// Capa de acceso al API. Centraliza las llamadas HTTP con axios.
import axios from 'axios'
import type { Item } from '../types'

// Instancia de axios con la URL base del API.
// import.meta.env.VITE_API_URL viene del archivo .env (Vite solo expone
// las variables que empiezan por VITE_). En este proyecto vale "/api",
// que es el mismo origen servido por el proxy Traefik -> sin CORS.
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? '/api',
  headers: { Accept: 'application/json' },
})

// GET /api/items -> lista de items.
export async function getItems(): Promise<Item[]> {
  const { data } = await api.get<Item[]>('/items')
  return data
}

// GET /api/items/:id -> un item por id.
export async function getItem(id: number): Promise<Item> {
  const { data } = await api.get<Item>(`/items/${id}`)
  return data
}
