<script setup>
import NavbarEw from '@/components/NavbarEw.vue'
import RodapeEw from '@/components/RodapeEw.vue'
import ToastAviso from '@/components/ToastAviso.vue'
</script>

<template>
  <NavbarEw />
  <RouterView />
  <RodapeEw />
  <ToastAviso />
</template>
