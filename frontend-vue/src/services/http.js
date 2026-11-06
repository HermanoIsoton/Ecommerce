import axios from 'axios'

const raiz = import.meta.env.VITE_API_BASE_URL ?? ''  

export const http = axios.create({
  baseURL: `${raiz}/api`,
  headers: { Accept: 'application/json' },
  withCredentials: true,
  withXSRFToken: true,
  timeout: 30000,
})

export const obterCsrf = () =>
  axios.get(`${raiz}/sanctum/csrf-cookie`, { withCredentials: true, timeout: 30000 })