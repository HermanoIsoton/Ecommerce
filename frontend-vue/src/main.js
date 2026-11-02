// importa modulo createApp
import { createApp } from 'vue'
// importa o arquivo app.vue
import App from './App.vue'
// rotas (Inicio, Carrinho, Minha conta)
import router from './router'

// Bootstrap e icones (antes vinham do CDN). O main.css vem depois para sobrescrever as variaveis.
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import './assets/main.css'

// render
createApp(App).use(router).mount('#app')
