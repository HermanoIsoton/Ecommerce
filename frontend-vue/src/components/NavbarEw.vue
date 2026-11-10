<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { totalUnidades } from '@/stores/carrinho'
import { filtroTipo, termoBusca } from '@/stores/catalogo'
import { avisar } from '@/stores/toast'
import {
  carregandoEnderecos,
  enderecos,
  iniciarEnderecosDoUsuario,
} from '@/stores/enderecos'
import { logout } from '@/services/auth'
import { listarEnderecos } from '@/services/enderecos'
import AuthModal from '@/components/AuthModal.vue'

const props = defineProps({
  usuario: {
    type: Object,
    default: null,
  },
  autenticacaoCarregada: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['authenticated', 'logged-out'])

const rota = useRoute()
const router = useRouter()
const contaRef = ref(null)
const campoBusca = ref(termoBusca.value)
const iniciaisUsuario = computed(() =>
  props.usuario?.name
    ?.split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((parte) => parte[0].toUpperCase())
    .join('') || '--'
)
const enderecoPrincipal = computed(() =>
  enderecos.value.find((endereco) => endereco.is_primary) ?? enderecos.value[0] ?? null
)
const menuContaAberto = ref(false)
const modalAutenticacao = ref('')
const navegarParaEnderecosAposLogin = ref(false)

watch(() => rota.fullPath, () => {
  menuContaAberto.value = false
})

watch(termoBusca, (termo) => {
  campoBusca.value = termo
})

async function carregarEnderecoPrincipal() {
  const userId = props.usuario?.id
  iniciarEnderecosDoUsuario(userId ?? null)
  if (!userId) {
    return
  }

  try {
    await listarEnderecos(userId)
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

watch(
  () => props.usuario?.id,
  carregarEnderecoPrincipal,
  { immediate: true }
)

function fecharMenuAoClicarFora(evento) {
  if (!contaRef.value?.contains(evento.target)) {
    menuContaAberto.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', fecharMenuAoClicarFora)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', fecharMenuAoClicarFora)
})

function filtrarPor(tipo) {
  filtroTipo.value = tipo
}

async function buscar() {
  termoBusca.value = campoBusca.value
  if (rota.name !== 'home') {
    await router.push({ name: 'home', hash: '#catalogo' })
  }
  document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth' })
}

function abrirModalAutenticacao(modo) {
  menuContaAberto.value = false
  modalAutenticacao.value = modo
}

defineExpose({ abrirModalAutenticacao })

async function abrirEnderecos() {
  if (!props.usuario) {
    navegarParaEnderecosAposLogin.value = true
    abrirModalAutenticacao('login')
    return
  }

  await router.push({ name: 'conta', query: { aba: 'enderecos' } })
}

async function aposAutenticacao(usuario) {
  emit('authenticated', usuario)
  modalAutenticacao.value = ''

  if (navegarParaEnderecosAposLogin.value) {
    navegarParaEnderecosAposLogin.value = false
    await router.push({ name: 'conta', query: { aba: 'enderecos' } })
  }
}

async function sair() {
  menuContaAberto.value = false

  try {
    await logout()
    emit('logged-out')
    avisar('Você saiu da sua conta.')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}
</script>

<template>
  <header class="ew-header">
    <div class="ew-navbar" data-bs-theme="dark">
      <div class="container ew-header-main">
        <RouterLink class="ew-brand" :to="{ name: 'home' }" aria-label="EletricWay - página inicial">
          <svg width="38" height="38" viewBox="0 0 120 120" role="img" aria-label="Símbolo EletricWay" xmlns="http://www.w3.org/2000/svg">
            <path d="M60 18 A42 42 0 1 1 22.5 79" fill="none" stroke="#2EF08A" stroke-width="8" stroke-linecap="round"/>
            <polygon points="66,30 40,64 56,64 50,92 80,54 63,54" fill="#FFFFFF"/>
          </svg>
          <span>Eletric<span class="way">Way</span></span>
        </RouterLink>

        <button class="ew-location" type="button" @click="abrirEnderecos">
          <i class="bi bi-geo-alt" aria-hidden="true"></i>
          <span class="ew-location-copy">
            <small>Entrega</small>
            <strong v-if="carregandoEnderecos">Carregando endereço...</strong>
            <template v-else-if="enderecoPrincipal">
              <strong>{{ enderecoPrincipal.street }}, {{ enderecoPrincipal.number }}</strong>
              <small>{{ enderecoPrincipal.city }}/{{ enderecoPrincipal.state }} · CEP {{ enderecoPrincipal.cep }}</small>
            </template>
            <strong v-else-if="usuario">Adicione um endereço</strong>
            <strong v-else>Entre para adicionar endereço</strong>
          </span>
        </button>

        <form class="ew-search-form" role="search" @submit.prevent="buscar">
          <label class="visually-hidden" for="campoBusca">Buscar veículos</label>
          <div class="input-group ew-search">
            <input
              id="campoBusca"
              v-model="campoBusca"
              class="form-control"
              type="search"
              placeholder="Buscar veículos elétricos"
              autocomplete="off"
            >
            <button class="btn ew-search-button" type="submit" aria-label="Buscar">
              <i class="bi bi-search" aria-hidden="true"></i>
            </button>
          </div>
        </form>

        <div class="ew-header-actions">
          <div
            ref="contaRef"
            class="ew-account"
            @mouseenter="menuContaAberto = true"
            @mouseleave="menuContaAberto = false"
            @focusin="menuContaAberto = true"
          >
            <button
              class="ew-action-btn"
              type="button"
              :disabled="!autenticacaoCarregada"
              aria-haspopup="true"
              :aria-expanded="menuContaAberto"
              @click="menuContaAberto = true"
              @keydown.esc="menuContaAberto = false"
            >
              <span
                v-if="usuario"
                class="ew-account-avatar"
                aria-hidden="true"
              >{{ iniciaisUsuario }}</span>
              <i v-else class="bi bi-person-circle" aria-hidden="true"></i>
              <span>
                <small>Olá, {{ usuario?.name || 'entre' }}</small>
                <strong>Minha conta</strong>
              </span>
              <i class="bi bi-chevron-down ew-chevron" aria-hidden="true"></i>
            </button>

            <div v-if="menuContaAberto" class="ew-account-menu">
              <template v-if="usuario">
                <RouterLink class="ew-account-option" :to="{ name: 'conta' }">
                  Informações da conta
                </RouterLink>
                <button type="button" class="ew-account-option" @click="sair">
                  Sair da conta
                </button>
              </template>
              <template v-else>
                <button type="button" class="ew-account-option" @click="abrirModalAutenticacao('login')">
                  Fazer login
                </button>
                <button type="button" class="ew-account-option" @click="abrirModalAutenticacao('register')">
                  Cadastrar-se
                </button>
              </template>
            </div>
          </div>

          <RouterLink
            class="ew-action-btn ew-cart-link"
            :to="{ name: 'carrinho' }"
            aria-label="Abrir carrinho"
            :aria-current="rota.name === 'carrinho' ? 'page' : undefined"
          >
            <span class="ew-cart-icon">
              <i class="bi bi-cart3" aria-hidden="true"></i>
              <span v-if="totalUnidades > 0" class="ew-badge">{{ totalUnidades }}</span>
            </span>
            <span><small>Seu</small><strong>Carrinho</strong></span>
          </RouterLink>
        </div>
      </div>
    </div>

  </header>

  <nav class="ew-subnav" aria-label="Navegação principal">
    <div class="container ew-subnav-inner">
      <RouterLink
        class="ew-subnav-link"
        :class="{ active: rota.name === 'home' }"
        :to="{ name: 'home' }"
        :aria-current="rota.name === 'home' ? 'page' : undefined"
      >
        <i class="bi bi-house-door" aria-hidden="true"></i>Início
      </RouterLink>
      <RouterLink
        class="ew-subnav-link"
        :class="{ active: rota.name === 'home' && filtroTipo === 'autopropelido' }"
        :to="{ name: 'home', hash: '#catalogo' }"
        @click="filtrarPor('autopropelido')"
      >Autopropelidos</RouterLink>
      <RouterLink
        class="ew-subnav-link"
        :class="{ active: rota.name === 'home' && filtroTipo === 'ciclomotor' }"
        :to="{ name: 'home', hash: '#catalogo' }"
        @click="filtrarPor('ciclomotor')"
      >Ciclomotores</RouterLink>
      <RouterLink class="ew-subnav-link" :to="{ name: 'home', hash: '#tipos' }">
        Qual escolher?
      </RouterLink>
      <div v-if="usuario" class="ew-subnav-actions">
        <RouterLink
          class="ew-subnav-link"
          :class="{ active: rota.name === 'carrinho' }"
          :to="{ name: 'carrinho' }"
          :aria-current="rota.name === 'carrinho' ? 'page' : undefined"
        >
          <i class="bi bi-cart3" aria-hidden="true"></i>Carrinho
          <span v-if="totalUnidades > 0" class="ew-subnav-badge">{{ totalUnidades }}</span>
        </RouterLink>
        <RouterLink
          class="ew-subnav-link"
          :class="{ active: rota.name === 'conta' }"
          :to="{ name: 'conta' }"
          :aria-current="rota.name === 'conta' ? 'page' : undefined"
        >
          <i class="bi bi-person-circle" aria-hidden="true"></i>Minha conta
        </RouterLink>
        <RouterLink
          v-if="usuario.role === 'admin'"
          class="ew-subnav-link"
          :class="{ active: rota.name === 'admin' }"
          :to="{ name: 'admin' }"
          :aria-current="rota.name === 'admin' ? 'page' : undefined"
          aria-label="Abrir área administrativa"
          title="Área administrativa"
        >
          <i class="bi bi-gear" aria-hidden="true"></i>Administração
        </RouterLink>
      </div>
    </div>
  </nav>

  <AuthModal
    v-if="modalAutenticacao"
    :mode="modalAutenticacao"
    @close="modalAutenticacao = ''"
    @change-mode="modalAutenticacao = $event"
    @authenticated="aposAutenticacao"
  />
</template>
