/// <reference types="vite/client" />

// Declaramos las variables de entorno propias para que TypeScript las conozca.
interface ImportMetaEnv {
  readonly VITE_API_URL: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
