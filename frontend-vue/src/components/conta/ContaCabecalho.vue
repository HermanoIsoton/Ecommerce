<script setup>
import { computed } from 'vue'
import { ROTULO_PAPEL } from '@/utils/rotulos'

const props = defineProps({
  usuario: { type: Object, required: true },
})

defineEmits(['sair'])

const iniciais = computed(() =>
  props.usuario.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((parte) => parte[0].toUpperCase())
    .join('')
)
</script>

<template>
  <section class="card card-ew mb-4" aria-label="Resumo da conta">
    <div class="card-body d-flex flex-wrap align-items-center gap-3">
      <div class="avatar" aria-hidden="true">{{ iniciais || '--' }}</div>
      <div class="flex-grow-1">
        <h1 class="h4 fw-bold mb-1">{{ usuario.name }}</h1>
        <p class="text-secondary mb-0">{{ usuario.email }}</p>
      </div>
      <span class="badge rounded-pill text-bg-light border fs-6">{{ ROTULO_PAPEL[usuario.role] }}</span>
      <button type="button" class="btn btn-outline-danger" @click="$emit('sair')">
        <i class="bi bi-box-arrow-right me-1"></i>Sair
      </button>
    </div>
  </section>
</template>
