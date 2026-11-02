<script setup>
import { computed } from 'vue'
import { formatarMoeda } from '@/utils/formatadores'
import { ROTULO_TIPO } from '@/utils/rotulos'

const props = defineProps({
  produto: { type: Object, required: true },
})

defineEmits(['adicionar'])

const semEstoque = computed(() => Number(props.produto.stock) <= 0)
</script>

<template>
  <div class="col-12 col-sm-6 col-xl-4">
    <article class="card produto-card h-100">
      <a class="produto-img text-decoration-none" href="#" :aria-label="`Ver ${produto.name}`" @click.prevent>
        <img v-if="produto.image_url" :src="produto.image_url" :alt="produto.name" loading="lazy">
        <i v-else class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
        <span class="badge badge-tipo" :class="produto.type === 'ciclomotor' ? 'text-bg-warning' : 'text-bg-success'">
          {{ ROTULO_TIPO[produto.type] }}
        </span>
      </a>
      <div class="card-body d-flex flex-column gap-3">
        <div>
          <p class="small text-secondary mb-1">{{ produto.category.name }}</p>
          <h3 class="h5 fw-bold mb-1">{{ produto.name }}</h3>
          <p class="small text-secondary mb-0">{{ produto.description }}</p>
        </div>
        <ul class="spec-lista">
          <li><strong>{{ produto.range_km }} km</strong>autonomia</li>
          <li><strong>{{ produto.top_speed_kmh }} km/h</strong>velocidade</li>
          <li><strong>{{ produto.power }} W</strong>potência</li>
        </ul>
        <p class="small text-secondary mb-0">
          <template v-if="produto.is_battery_removable">
            <i class="bi bi-battery-charging me-1" aria-hidden="true"></i>Bateria removível
          </template>
          <template v-else>
            <i class="bi bi-battery-full me-1" aria-hidden="true"></i>Bateria fixa
          </template>
          · {{ produto.battery_capaciy }} Wh · {{ produto.battery_voltage_v }} V<br>
          {{ produto.charge_time_h }} h de carga · {{ produto.warranty_months }} meses de garantia
        </p>
        <div class="mt-auto d-flex align-items-end justify-content-between gap-2">
          <div>
            <span class="preco">{{ formatarMoeda(produto.price) }}</span>
            <div class="small" :class="semEstoque ? 'text-danger' : 'text-secondary'">
              {{ semEstoque ? 'Esgotado' : `${produto.stock} em estoque` }}
            </div>
          </div>
          <button type="button" class="btn btn-ew-accent" :disabled="semEstoque" @click="$emit('adicionar', produto)">
            <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Adicionar
          </button>
        </div>
      </div>
    </article>
  </div>
</template>
