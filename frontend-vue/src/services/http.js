import axios from 'axios'

const raiz = import.meta.env.VITE_API_BASE_URL ?? ''  

export const http = axios.create({
  baseURL: `${raiz}/api`,
  headers: { Accept: 'application/json' },
  withCredentials: true,
  withXSRFToken: true,
  timeout: 30000,
})

let tokenCarrinho = ''

export function definirTokenCarrinho(token) {
  tokenCarrinho = token
}

http.interceptors.request.use((config) => {
  if (tokenCarrinho) {
    config.headers.set('X-Cart-Token', tokenCarrinho)
  }

  return config
})

export const obterCsrf = () =>
  axios.get(`${raiz}/sanctum/csrf-cookie`, { withCredentials: true, timeout: 30000 })