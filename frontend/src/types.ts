// Tipos compartidos de la app.
// Una "interface" describe la forma de un objeto. Aquí modelamos lo que
// devuelve el backend en GET /api/items.

export interface Item {
  id: number
  name: string
  description: string | null
  price: number
  created_at: string
  updated_at: string
}
