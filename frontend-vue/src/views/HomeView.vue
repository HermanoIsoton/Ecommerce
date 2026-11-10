<script setup>
import HeroSecao from '@/components/home/HeroSecao.vue'
import BeneficiosSecao from '@/components/home/BeneficiosSecao.vue'
import TiposVeiculoSecao from '@/components/home/TiposVeiculoSecao.vue'
import CatalogoSecao from '@/components/home/CatalogoSecao.vue'

defineProps({
  usuario: {
    type: Object,
    default: null,
  },
})
</script>

<template>
  <main class="flex-grow-1">
    <HeroSecao :usuario="usuario"/>
    <BeneficiosSecao />
    <TiposVeiculoSecao />
    <CatalogoSecao />
  </main>
</template>
