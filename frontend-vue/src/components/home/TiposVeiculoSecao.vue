<script setup>
import { filtroTipo } from '@/stores/catalogo'
</script>

<template>
  <section class="py-5 bg-white border-bottom" id="tipos" aria-labelledby="tituloTipos">
    <div class="container">
      <div class="text-center mx-auto mb-4" style="max-width: 46rem;">
        <h2 class="fw-bold" id="tituloTipos">Autopropelido ou ciclomotor?</h2>
        <p class="text-secondary mb-0">Os dois são elétricos, mas seguem regras de trânsito diferentes. Entenda a diferença antes de escolher o seu.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6">
          <article class="card tipo-card tipo-autopropelido h-100">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                  <h3 class="h4 fw-bold mb-1">Autopropelido</h3>
                  <p class="text-secondary mb-0">Patinetes, bikes elétricas e scooters pequenas</p>
                </div>
                <span class="badge text-bg-success fs-6">Sem CNH</span>
              </div>
              <ul class="tipo-lista mb-4">
                <li><i class="bi bi-person-check" aria-hidden="true"></i><span><strong>Sem CNH:</strong> não precisa de habilitação para conduzir.</span></li>
                <li><i class="bi bi-file-earmark-check" aria-hidden="true"></i><span><strong>Sem burocracia:</strong> não exige registro, emplacamento nem licenciamento.</span></li>
                <li><i class="bi bi-speedometer2" aria-hidden="true"></i><span><strong>Velocidade limitada</strong> a 32 km/h.</span></li>
                <li><i class="bi bi-lightning-charge" aria-hidden="true"></i><span><strong>Potência</strong> de até 1.000 W.</span></li>
                <li><i class="bi bi-signpost-split" aria-hidden="true"></i><span>Circula em ciclovias, ciclofaixas e áreas liberadas pela prefeitura. Não pode andar em rodovias.</span></li>
                <li><i class="bi bi-shield-check" aria-hidden="true"></i><span>Use sempre capacete. A obrigatoriedade pode variar conforme o município.</span></li>
              </ul>
              <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-ew mt-auto align-self-start" @click="filtroTipo = 'autopropelido'">Ver autopropelidos</RouterLink>
            </div>
          </article>
        </div>

        <div class="col-md-6">
          <article class="card tipo-card tipo-ciclomotor h-100">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                  <h3 class="h4 fw-bold mb-1">Ciclomotor</h3>
                  <p class="text-secondary mb-0">Scooters e ciclomotores elétricos mais potentes</p>
                </div>
                <span class="badge text-bg-warning fs-6">CNH A ou ACC</span>
              </div>
              <ul class="tipo-lista mb-4">
                <li><i class="bi bi-person-vcard" aria-hidden="true"></i><span><strong>Habilitação:</strong> CNH categoria A ou ACC (Autorização para Conduzir Ciclomotor).</span></li>
                <li><i class="bi bi-card-text" aria-hidden="true"></i><span><strong>Documentação:</strong> registro, emplacamento e licenciamento obrigatórios.</span></li>
                <li><i class="bi bi-speedometer2" aria-hidden="true"></i><span><strong>Velocidade</strong> de até 50 km/h.</span></li>
                <li><i class="bi bi-lightning-charge" aria-hidden="true"></i><span><strong>Potência</strong> do motor elétrico de 1.000 W até 4.000 W.</span></li>
                <li><i class="bi bi-signpost-2" aria-hidden="true"></i><span>Circula nas vias comuns, com as mesmas regras das motocicletas.</span></li>
                <li><i class="bi bi-shield-check" aria-hidden="true"></i><span>Capacete de moto obrigatório.</span></li>
              </ul>
              <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-ew mt-auto align-self-start" @click="filtroTipo = 'ciclomotor'">Ver ciclomotores</RouterLink>
            </div>
          </article>
        </div>
      </div>

      <p class="small text-secondary text-center mt-4 mb-0">
        Informações baseadas na Resolução 996/2023 do Contran, em vigor em 2026. Podem existir regras locais: confirme com o Detran e a prefeitura da sua cidade.
        Fontes:
        <a href="https://www.portaldotransito.com.br/noticias/fiscalizacao-e-legislacao/detran-explica-como-conduzir-bicicletas-eletricas-autopropelidos-ou-ciclomotores/amp/" target="_blank" rel="noopener">Portal do Trânsito</a>
        e
        <a href="https://revistacarro.com.br/regras-para-bicicletas-eletricas-patinetes-e-ciclomotores-vai-mudar-em-2026-confira/" target="_blank" rel="noopener">Revista Carro</a>.
      </p>
    </div>
  </section>
</template>
