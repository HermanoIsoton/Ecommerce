<script setup>
import { computed } from 'vue'
import { formatarMoeda } from '@/utils/formatadores'
import { ROTULO_TIPO } from '@/utils/rotulos'

const props = defineProps({
  item: { type: Object, required: true },
  busy: { type: Boolean, default: false },
})

defineEmits(['aumentar', 'diminuir', 'remover'])

const produto = computed(() => props.item.product)
const subtotal = computed(() => props.item.subtotal)
const noLimiteDoEstoque = computed(() =>
  !produto.value.is_active || props.item.quantity >= produto.value.stock
)
</script>

<template>
  <li class="list-group-item p-3 p-md-4">
    <div class="d-flex gap-3">
      <div class="carrinho-img d-flex align-items-center justify-content-center flex-shrink-0">
        <img v-if="produto.image_url" class="w-100 h-100 object-fit-contain" :src="produto.image_url" :alt="produto.name" loading="lazy">
        <i v-else class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex flex-wrap justify-content-between gap-2">
          <div>
            <h2 class="h6 fw-bold mb-1">{{ produto.name }}</h2>
            <p class="small text-secondary mb-2">
              {{ ROTULO_TIPO[produto.type] }} · {{ produto.range_km }} km de autonomia · {{ produto.top_speed_kmh }} km/h
            </p>
          </div>
          <strong class="fs-5">{{ formatarMoeda(subtotal) }}</strong>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="qtd-controle btn-group align-items-center rounded-3 border bg-white overflow-hidden" role="group" :aria-label="`Quantidade de ${produto.name}`">
              <button class="btn btn-outline-secondary border-0 px-3 py-2" type="button" aria-label="Diminuir quantidade" :disabled="busy || item.quantity <= 1" @click="$emit('diminuir')">
                <i class="bi bi-dash-lg"></i>
              </button>
              <span class="px-2 text-center fw-bold" aria-live="polite">{{ item.quantity }}</span>
              <button class="btn btn-outline-secondary border-0 px-3 py-2" type="button" aria-label="Aumentar quantidade" :disabled="busy || noLimiteDoEstoque" @click="$emit('aumentar')">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
            <span class="small text-secondary">{{ formatarMoeda(item.unit_price) }} cada</span>
          </div>
          <button type="button" class="btn btn-sm btn-outline-danger" :disabled="busy" @click="$emit('remover')">
            <i class="bi bi-trash me-1" aria-hidden="true"></i>Remover
          </button>
        </div>
        <p v-if="!item.is_available" class="small text-danger mt-2 mb-0">
          {{ item.availability_message }}. Reduza a quantidade ou remova este produto para continuar.
        </p>
        <p v-else-if="noLimiteDoEstoque" class="small text-warning-emphasis mt-2 mb-0">
          Quantidade máxima disponível em estoque.
        </p>
      </div>
    </div>
  </li>
</template>
