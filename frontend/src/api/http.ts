// Cliente HTTP genérico y reutilizable.
// A diferencia de api/items.ts (que tiene una función por endpoint y devuelve
// un tipo fijo), aquí cada método es GENÉRICO: el tipo de la respuesta lo
// decides tú al llamar. Sirve para golpear CUALQUIER API, online o local.
//
//   await http.get('/items')                 -> data es 'any' (sin tipo, todo vale)
//   await http.get<Item[]>('/items')         -> data es Item[] (tipado)
import axios from 'axios'
import type { AxiosRequestConfig } from 'axios'

// Una sola instancia. Para APIs online puedes pasar la URL completa en cada
// llamada, o crear otra instancia con baseURL distinta (ver ejemplo abajo).
const client = axios.create({
  headers: { Accept: 'application/json' },
})

// <T = any> significa: si no dices el tipo, será 'any' (todo vale, sin que
// TypeScript te moleste). Si lo dices, usa ese tipo y te da autocompletado.
export const http = {
  get: <T = any>(url: string, config?: AxiosRequestConfig) =>
    client.get<T>(url, config).then((r) => r.data),

  post: <T = any>(url: string, body?: unknown, config?: AxiosRequestConfig) =>
    client.post<T>(url, body, config).then((r) => r.data),

  put: <T = any>(url: string, body?: unknown, config?: AxiosRequestConfig) =>
    client.put<T>(url, body, config).then((r) => r.data),

  delete: <T = any>(url: string, config?: AxiosRequestConfig) =>
    client.delete<T>(url, config).then((r) => r.data),
}

// Helper opcional: crea un cliente "atado" a una baseURL concreta.
// Útil si en la entrevista te piden consumir, p.ej., una API pública:
//
//   const gh = createClient('https://api.github.com')
//   const user = await gh.get('/users/octocat')   // sin tipo, exploras libre
//   const repos = await gh.get<Repo[]>('/users/octocat/repos')  // tipado
export function createClient(baseURL: string) {
  const c = axios.create({ baseURL, headers: { Accept: 'application/json' } })
  return {
    get: <T = any>(url: string, config?: AxiosRequestConfig) =>
      c.get<T>(url, config).then((r) => r.data),
    post: <T = any>(url: string, body?: unknown, config?: AxiosRequestConfig) =>
      c.post<T>(url, body, config).then((r) => r.data),
    put: <T = any>(url: string, body?: unknown, config?: AxiosRequestConfig) =>
      c.put<T>(url, body, config).then((r) => r.data),
    delete: <T = any>(url: string, config?: AxiosRequestConfig) =>
      c.delete<T>(url, config).then((r) => r.data),
  }
}
