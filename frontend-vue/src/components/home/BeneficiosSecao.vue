<script setup>
const beneficios = [
  { icone: 'bi-speedometer2', titulo: 'Ficha completa', texto: 'Autonomia, potência e velocidade' },
  { icone: 'bi-battery-charging', titulo: 'Bateria detalhada', texto: 'Tempo de carga e voltagem' },
  { icone: 'bi-shield-check', titulo: 'Garantia informada', texto: 'Prazo em meses em cada item' },
  { icone: 'bi-truck', titulo: 'Pedido acompanhado', texto: 'Do pagamento à entrega' },
]
</script>

<template>
  <section class="bg-white border-bottom" aria-label="Diferenciais">
    <div class="container py-4">
      <div class="row g-3">
        <div v-for="beneficio in beneficios" :key="beneficio.titulo" class="col-6 col-lg-3">
          <div class="beneficio">
            <i class="bi" :class="beneficio.icone" aria-hidden="true"></i>
            <div>
              <strong class="d-block">{{ beneficio.titulo }}</strong>
              <span class="small text-secondary">{{ beneficio.texto }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
