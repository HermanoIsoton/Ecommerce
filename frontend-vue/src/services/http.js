import axios from 'axios'

export const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost/api',
  headers: { Accept: 'application/json' }, 
  timeout: 10000,
})