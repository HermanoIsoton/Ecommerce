<script setup>
import { onMounted, ref } from 'vue'
import NavbarEw from '@/components/NavbarEw.vue'
import RodapeEw from '@/components/RodapeEw.vue'
import ToastAviso from '@/components/ToastAviso.vue'
import { obterUsuarioAtual } from '@/services/auth'
import { avisar } from '@/stores/toast'

const usuario = ref(null)

onMounted(async () => {
  try {
    usuario.value = await obterUsuarioAtual()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
})
</script>

<template>
  <NavbarEw
    :usuario="usuario"
    @authenticated="usuario = $event"
    @logged-out="usuario = null"
  />
  <RouterView :usuario="usuario" />
  <RodapeEw />
  <ToastAviso />
</template>
