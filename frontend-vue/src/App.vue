<script setup>
import { onMounted, ref } from 'vue'
import NavbarEw from '@/components/NavbarEw.vue'
import RodapeEw from '@/components/RodapeEw.vue'
import ToastAviso from '@/components/ToastAviso.vue'
import { obterUsuarioAtual } from '@/services/auth'
import { avisar } from '@/stores/toast'

const usuario = ref(null)
const autenticacaoCarregada = ref(false)

onMounted(async () => {
  try {
    usuario.value = await obterUsuarioAtual()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    autenticacaoCarregada.value = true
  }
})
</script>

<template>
  <NavbarEw
    :usuario="usuario"
    @authenticated="usuario = $event"
    @logged-out="usuario = null"
  />
  <RouterView v-slot="{ Component, route }">
    <component
      :is="Component"
      @usuario-atualizado="usuario = $event"
      v-bind="{ usuario, ...(route.name === 'conta' ? { autenticacaoCarregada } : {}) }"
    />
  </RouterView>
  <RodapeEw />
  <ToastAviso />
</template>
