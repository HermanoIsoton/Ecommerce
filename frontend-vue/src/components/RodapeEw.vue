<template>
  <footer class="ew-footer py-4 mt-auto">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
      <span class="fw-bold text-white">Eletric<span style="color: var(--ew-green);">Way</span></span>
      <nav class="d-flex gap-3 small" aria-label="Rodapé">
        <RouterLink class="text-decoration-none" :to="{ name: 'home' }">Início</RouterLink>
        <RouterLink class="text-decoration-none" :to="{ name: 'conta' }">Minha conta</RouterLink>
        <RouterLink class="text-decoration-none" :to="{ name: 'carrinho' }">Carrinho</RouterLink>
      </nav>
      <small>&copy; 2026 EletricWay. Todos os direitos reservados.</small>
    </div>
  </footer>
</template>
