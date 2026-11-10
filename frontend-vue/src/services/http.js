import axios from 'axios'

export const http = axios.create({
  baseURL: '/api',
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
  axios.get('/sanctum/csrf-cookie', { withCredentials: true, timeout: 30000 })