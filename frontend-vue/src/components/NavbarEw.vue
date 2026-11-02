<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { totalUnidades } from '@/stores/carrinho'
import { filtroTipo, termoBusca } from '@/stores/catalogo'

const rota = useRoute()

const menuAberto = ref(false)
const campoBusca = ref('')

// Fecha o menu do celular ao trocar de pagina
watch(() => rota.fullPath, () => {
  menuAberto.value = false
})

function filtrarPor(tipo) {
  filtroTipo.value = tipo
  menuAberto.value = false
}

function buscar() {
  termoBusca.value = campoBusca.value
  document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth' })
}
</script>

<template>
  <header class="sticky-top">
    <nav class="navbar navbar-expand-lg ew-navbar" data-bs-theme="dark" aria-label="Menu principal">
      <div class="container">
        <RouterLink class="ew-brand" :to="{ name: 'home' }" aria-label="EletricWay - página inicial">
          <svg width="38" height="38" viewBox="0 0 120 120" role="img" aria-label="Símbolo EletricWay" xmlns="http://www.w3.org/2000/svg">
            <path d="M60 18 A42 42 0 1 1 22.5 79" fill="none" stroke="#2EF08A" stroke-width="8" stroke-linecap="round"/>
            <polygon points="66,30 40,64 56,64 50,92 80,54 63,54" fill="#FFFFFF"/>
          </svg>
          <span>Eletric<span class="way">Way</span></span>
        </RouterLink>

        <div class="d-flex align-items-center gap-2 order-lg-last">
          <RouterLink
            class="ew-icon-btn"
            :to="{ name: 'conta' }"
            aria-label="Minha conta"
            :aria-current="rota.name === 'conta' ? 'page' : undefined"
          >
            <i class="bi bi-person-circle" :style="rota.name === 'conta' ? { color: 'var(--ew-green)' } : null"></i>
          </RouterLink>
          <RouterLink
            class="ew-icon-btn"
            :to="{ name: 'carrinho' }"
            aria-label="Carrinho"
            :aria-current="rota.name === 'carrinho' ? 'page' : undefined"
          >
            <i class="bi bi-cart3" :style="rota.name === 'carrinho' ? { color: 'var(--ew-green)' } : null"></i>
            <span v-if="totalUnidades > 0" class="ew-badge">{{ totalUnidades }}</span>
          </RouterLink>
          <button
            class="navbar-toggler ms-1"
            :class="{ collapsed: !menuAberto }"
            type="button"
            aria-controls="menuPrincipal"
            :aria-expanded="menuAberto"
            aria-label="Abrir menu"
            @click="menuAberto = !menuAberto"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <div class="collapse navbar-collapse" :class="{ show: menuAberto }" id="menuPrincipal">
          <ul class="navbar-nav mx-lg-4 mb-2 mb-lg-0">
            <li class="nav-item">
              <RouterLink
                class="nav-link"
                :class="{ active: rota.name === 'home' }"
                :to="{ name: 'home' }"
                :aria-current="rota.name === 'home' ? 'page' : undefined"
              >Início</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ name: 'home', hash: '#catalogo' }" @click="filtrarPor('autopropelido')">Autopropelidos</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ name: 'home', hash: '#catalogo' }" @click="filtrarPor('ciclomotor')">Ciclomotores</RouterLink>
            </li>
            <li v-if="rota.name === 'home'" class="nav-item">
              <RouterLink class="nav-link" :to="{ name: 'home', hash: '#tipos' }">Qual escolher?</RouterLink>
            </li>
          </ul>

          <!-- A busca so aparece na pagina inicial, onde fica o catalogo -->
          <form v-if="rota.name === 'home'" class="d-flex flex-grow-1 ms-lg-auto my-2 my-lg-0" role="search" @submit.prevent="buscar">
            <label class="visually-hidden" for="campoBusca">Buscar veículos</label>
            <input
              v-model="campoBusca"
              class="form-control"
              type="search"
              id="campoBusca"
              placeholder="Buscar veículos elétricos"
              autocomplete="off"
            >
            <button class="btn btn-ew-accent ms-2" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
          </form>
        </div>
      </div>
    </nav>
  </header>
</template>
