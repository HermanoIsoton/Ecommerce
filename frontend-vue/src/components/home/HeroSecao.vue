<script setup>
defineProps({
  usuario: {
    type: Object,
    default: null,
  },
})
</script>
<template>
  <section class="hero py-5">
    <div class="container py-lg-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <p class="text-uppercase small fw-semibold mb-2" style="letter-spacing: 0.12em; color: var(--ew-green);">Mobilidade elétrica</p>
          <h1 class="display-4 mb-3">
            <template v-if="usuario?.name">Olá, {{ usuario.name }}! </template>
            Seu caminho começa <span class="destaque">sem fazer barulho</span>
          </h1>
          <p class="lead mb-4">Autopropelidos e ciclomotores elétricos com autonomia, velocidade, bateria e garantia explicadas em cada produto.</p>
          <div class="d-flex flex-wrap gap-2">
            <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-ew-accent btn-lg px-4">Ver catálogo</RouterLink>
            <RouterLink :to="{ name: 'conta' }" class="btn btn-outline-light btn-lg px-4">Minha conta</RouterLink>
          </div>
        </div>
        <div class="col-lg-5 hero-visual" aria-hidden="true">
          <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
            <rect width="120" height="120" rx="30" fill="#0B2A4A" stroke="#2EF08A" stroke-opacity="0.35" stroke-width="1.5"/>
            <path d="M60 18 A42 42 0 1 1 22.5 79" fill="none" stroke="#2EF08A" stroke-width="8" stroke-linecap="round"/>
            <polygon points="66,30 40,64 56,64 50,92 80,54 63,54" fill="#FFFFFF"/>
          </svg>
        </div>
      </div>
    </div>
  </section>
</template>
