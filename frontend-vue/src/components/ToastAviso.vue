<script setup>
import { fecharAviso, toast } from '@/stores/toast'
</script>

<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div
      class="toast fade text-bg-dark"
      :class="{ show: toast.visivel }"
      role="status"
      aria-live="polite"
      aria-atomic="true"
    >
      <div class="d-flex">
        <div class="toast-body">
          <i
            class="bi me-2"
            :class="toast.tipo === 'info' ? 'bi-info-circle-fill text-info' : 'bi-check-circle-fill text-success'"
          ></i>
          <span>{{ toast.texto }}</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Fechar" @click="fecharAviso"></button>
      </div>
    </div>
  </div>
</template>
