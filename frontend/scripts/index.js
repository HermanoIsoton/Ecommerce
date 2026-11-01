// EletricWay - pagina inicial.
// ATENCAO: JS PROVISORIO DE DEMONSTRACAO.
// Os produtos abaixo sao ficticios e usam os mesmos nomes de campos da tabela `products`.
// Troque `produtosDemo` pelo retorno da API quando for integrar.

const produtosDemo = [
  {
    id: 1,
    category: { id: 1, name: 'Patinetes elétricos', slug: 'patinetes-eletricos' },
    name: 'Patinete Urban Sprint',
    slug: 'patinete-urban-sprint',
    description: 'Leve e dobrável, ideal para trajetos curtos na cidade.',
    image_url: '',
    price: 2890.0,
    stock: 12,
    is_active: true,
    model_year: 2026,
    seating_capacity: 1,
    type: 'autopropelido',
    power: 350,
    range_km: 25,
    top_speed_kmh: 25,
    weight_kg: 14.5,
    charge_time_h: 4,
    battery_capaciy: 280,
    battery_voltage_v: 36,
    is_battery_removable: false,
    warranty_months: 12,
  },
  {
    id: 2,
    category: { id: 2, name: 'Bicicletas elétricas', slug: 'bicicletas-eletricas' },
    name: 'Bike Elétrica Trilha 500',
    slug: 'bike-eletrica-trilha-500',
    description: 'Quadro em alumínio e bateria removível para recarregar em casa.',
    image_url: '',
    price: 6490.0,
    stock: 5,
    is_active: true,
    model_year: 2026,
    seating_capacity: 1,
    type: 'autopropelido',
    power: 500,
    range_km: 60,
    top_speed_kmh: 32,
    weight_kg: 23,
    charge_time_h: 5,
    battery_capaciy: 624,
    battery_voltage_v: 48,
    is_battery_removable: true,
    warranty_months: 12,
  },
  {
    id: 3,
    category: { id: 3, name: 'Scooters elétricas', slug: 'scooters-eletricas' },
    name: 'Scooter City 1000',
    slug: 'scooter-city-1000',
    description: 'Conforto para duas pessoas e bateria removível.',
    image_url: '',
    price: 12900.0,
    stock: 3,
    is_active: true,
    model_year: 2026,
    seating_capacity: 2,
    trunk_capacity_l: 20,
    type: 'ciclomotor',
    power: 1000,
    range_km: 70,
    top_speed_kmh: 45,
    weight_kg: 85,
    charge_time_h: 6,
    battery_capaciy: 1920,
    max_charge_power_kw: 1,
    battery_voltage_v: 60,
    is_battery_removable: true,
    warranty_months: 24,
  },
  {
    id: 4,
    category: { id: 3, name: 'Scooters elétricas', slug: 'scooters-eletricas' },
    name: 'Scooter Delivery Pro',
    slug: 'scooter-delivery-pro',
    description: 'Maior autonomia e baú traseiro para entregas.',
    image_url: '',
    price: 15990.0,
    stock: 8,
    is_active: true,
    model_year: 2026,
    seating_capacity: 2,
    trunk_capacity_l: 35,
    type: 'ciclomotor',
    power: 2000,
    range_km: 90,
    top_speed_kmh: 50,
    weight_kg: 98,
    charge_time_h: 7,
    battery_capaciy: 2880,
    max_charge_power_kw: 2,
    battery_voltage_v: 72,
    is_battery_removable: true,
    warranty_months: 24,
  },
  {
    id: 5,
    category: { id: 1, name: 'Patinetes elétricos', slug: 'patinetes-eletricos' },
    name: 'Patinete Max Cruiser',
    slug: 'patinete-max-cruiser',
    description: 'Rodas largas e suspensão para pisos irregulares.',
    image_url: '',
    price: 4390.0,
    stock: 7,
    is_active: true,
    model_year: 2026,
    seating_capacity: 1,
    type: 'autopropelido',
    power: 800,
    range_km: 45,
    top_speed_kmh: 32,
    weight_kg: 22,
    charge_time_h: 5.5,
    battery_capaciy: 576,
    battery_voltage_v: 48,
    is_battery_removable: false,
    warranty_months: 12,
  },
  {
    id: 6,
    category: { id: 3, name: 'Scooters elétricas', slug: 'scooters-eletricas' },
    name: 'Ciclomotor Retrô 3000',
    slug: 'ciclomotor-retro-3000',
    description: 'Visual clássico com motor elétrico de alto torque.',
    image_url: '',
    price: 18900.0,
    stock: 0,
    is_active: true,
    model_year: 2025,
    seating_capacity: 2,
    trunk_capacity_l: 25,
    type: 'ciclomotor',
    power: 3000,
    range_km: 80,
    top_speed_kmh: 50,
    weight_kg: 105,
    charge_time_h: 8,
    battery_capaciy: 3120,
    max_charge_power_kw: 1,
    battery_voltage_v: 72,
    is_battery_removable: false,
    warranty_months: 24,
  },
];

const listaProdutos = document.getElementById('listaProdutos');
const estadoVazio = document.getElementById('estadoVazio');
const campoBusca = document.getElementById('campoBusca');
const formBusca = document.getElementById('formBusca');
const botoesFiltro = document.querySelectorAll('.filtro-btn');
const selosCarrinho = document.querySelectorAll('.js-cart-badge');
const toastEl = document.getElementById('toastCarrinho');
const toastTexto = document.getElementById('toastCarrinhoTexto');

let filtroAtual = 'todos';
let termoBusca = '';
let itensNoCarrinho = 0; // so para demonstrar o contador do menu

function criarCardProduto(produto) {
  const semEstoque = Number(produto.stock) <= 0;
  const imagem = produto.image_url
    ? `<img src="${produto.image_url}" alt="${produto.name}" loading="lazy">`
    : '<i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>';
  const bateriaRemovivel = produto.is_battery_removable
    ? '<i class="bi bi-battery-charging me-1" aria-hidden="true"></i>Bateria removível'
    : '<i class="bi bi-battery-full me-1" aria-hidden="true"></i>Bateria fixa';

  return `
    <div class="col-12 col-sm-6 col-xl-4">
      <article class="card produto-card h-100">
        <a class="produto-img text-decoration-none" href="#" aria-label="Ver ${produto.name}">
          ${imagem}
          <span class="badge badge-tipo ${produto.type === 'ciclomotor' ? 'text-bg-warning' : 'text-bg-success'}">
            ${ROTULO_TIPO[produto.type]}
          </span>
        </a>
        <div class="card-body d-flex flex-column gap-3">
          <div>
            <p class="small text-secondary mb-1">${produto.category.name}</p>
            <h3 class="h5 fw-bold mb-1">${produto.name}</h3>
            <p class="small text-secondary mb-0">${produto.description}</p>
          </div>
          <ul class="spec-lista">
            <li><strong>${produto.range_km} km</strong>autonomia</li>
            <li><strong>${produto.top_speed_kmh} km/h</strong>velocidade</li>
            <li><strong>${produto.power} W</strong>potência</li>
          </ul>
          <p class="small text-secondary mb-0">
            ${bateriaRemovivel} · ${produto.battery_capaciy} Wh · ${produto.battery_voltage_v} V<br>
            ${produto.charge_time_h} h de carga · ${produto.warranty_months} meses de garantia
          </p>
          <div class="mt-auto d-flex align-items-end justify-content-between gap-2">
            <div>
              <span class="preco">${formatarMoeda(produto.price)}</span>
              <div class="small ${semEstoque ? 'text-danger' : 'text-secondary'}">
                ${semEstoque ? 'Esgotado' : produto.stock + ' em estoque'}
              </div>
            </div>
            <button type="button" class="btn btn-ew-accent js-adicionar" data-id="${produto.id}" ${semEstoque ? 'disabled' : ''}>
              <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Adicionar
            </button>
          </div>
        </div>
      </article>
    </div>`;
}

/* ---------- Filtros laterais (um para cada campo da tabela products) ---------- */
const formFiltros = document.getElementById('formFiltros');
const contagemResultados = document.getElementById('contagemResultados');
const selosFiltros = document.querySelectorAll('.js-filtros-ativos');

function valorDoCampo(produto, campo) {
  return campo === 'category_id' ? produto.category.id : produto[campo];
}

// Monta os filtros que dependem dos dados: categorias, tensoes e anos
function montarFiltrosDinamicos() {
  const categorias = new Map();
  const tensoes = new Set();
  const anos = new Set();

  produtosDemo.forEach((produto) => {
    categorias.set(produto.category.id, produto.category.name);
    tensoes.add(Number(produto.battery_voltage_v));
    if (produto.model_year) {
      anos.add(produto.model_year);
    }
  });

  const criarCheckbox = (idCampo, valor, rotulo) => `
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="${valor}" id="${idCampo}-${valor}">
      <label class="form-check-label small" for="${idCampo}-${valor}">${rotulo}</label>
    </div>`;

  document.getElementById('filtroCategorias').innerHTML = [...categorias]
    .map(([id, nome]) => criarCheckbox('f-categoria', id, nome))
    .join('');

  document.getElementById('filtroTensoes').innerHTML = [...tensoes]
    .sort((a, b) => a - b)
    .map((tensao) => criarCheckbox('f-tensao', tensao, `${tensao} V`))
    .join('');

  document.getElementById('f-model_year').insertAdjacentHTML(
    'beforeend',
    [...anos]
      .sort((a, b) => b - a)
      .map((ano) => `<option value="${ano}">${ano}</option>`)
      .join('')
  );
}

function lerFiltrosLaterais() {
  const faixas = [];
  formFiltros.querySelectorAll('[data-min], [data-max]').forEach((campo) => {
    if (campo.value === '') {
      return;
    }
    const ehMinimo = campo.dataset.min !== undefined;
    faixas.push({
      campo: ehMinimo ? campo.dataset.min : campo.dataset.max,
      ehMinimo,
      limite: Number(campo.value),
    });
  });

  const listas = [];
  formFiltros.querySelectorAll('[data-lista]').forEach((grupo) => {
    const marcados = [...grupo.querySelectorAll('input:checked')].map((caixa) => caixa.value);
    if (marcados.length > 0) {
      listas.push({ campo: grupo.dataset.lista, valores: marcados });
    }
  });

  const booleanos = [];
  formFiltros.querySelectorAll('[data-igual]').forEach((seletor) => {
    if (seletor.value !== '') {
      booleanos.push({ campo: seletor.dataset.igual, valor: seletor.value === '1' });
    }
  });

  const somenteEmEstoque = document.getElementById('filtroEstoque').checked;

  return { faixas, listas, booleanos, somenteEmEstoque };
}

function produtoPassaNosFiltros(produto, filtros) {
  const respeitaFaixas = filtros.faixas.every(({ campo, ehMinimo, limite }) => {
    const valor = valorDoCampo(produto, campo);
    if (valor === null || valor === undefined) {
      return false;
    }
    return ehMinimo ? Number(valor) >= limite : Number(valor) <= limite;
  });
  const respeitaListas = filtros.listas.every(({ campo, valores }) => {
    return valores.includes(String(valorDoCampo(produto, campo)));
  });
  const respeitaBooleanos = filtros.booleanos.every(({ campo, valor }) => Boolean(produto[campo]) === valor);
  const respeitaEstoque = !filtros.somenteEmEstoque || Number(produto.stock) > 0;

  return respeitaFaixas && respeitaListas && respeitaBooleanos && respeitaEstoque;
}

function renderizarProdutos() {
  const termo = termoBusca.trim().toLowerCase();
  const filtros = lerFiltrosLaterais();

  const filtrados = produtosDemo.filter((produto) => {
    if (!produto.is_active) {
      return false;
    }
    if (filtroAtual !== 'todos' && produto.type !== filtroAtual) {
      return false;
    }
    const combinaComBusca =
      !termo || produto.name.toLowerCase().includes(termo) || produto.category.name.toLowerCase().includes(termo);
    return combinaComBusca && produtoPassaNosFiltros(produto, filtros);
  });

  const filtrosAtivos =
    filtros.faixas.length + filtros.listas.length + filtros.booleanos.length + (filtros.somenteEmEstoque ? 1 : 0);
  selosFiltros.forEach((selo) => {
    selo.textContent = filtrosAtivos;
    selo.classList.toggle('d-none', filtrosAtivos === 0);
  });

  listaProdutos.innerHTML = filtrados.map(criarCardProduto).join('');
  estadoVazio.classList.toggle('d-none', filtrados.length > 0);
  contagemResultados.textContent = `${filtrados.length} ${filtrados.length === 1 ? 'veículo encontrado' : 'veículos encontrados'}`;
}

formFiltros.addEventListener('input', renderizarProdutos);
formFiltros.addEventListener('change', renderizarProdutos);
formFiltros.addEventListener('reset', () => setTimeout(renderizarProdutos, 0));

function atualizarSeloCarrinho() {
  selosCarrinho.forEach((selo) => {
    selo.textContent = itensNoCarrinho;
    selo.classList.toggle('d-none', itensNoCarrinho === 0);
  });
}

botoesFiltro.forEach((botao) => {
  botao.addEventListener('click', () => {
    filtroAtual = botao.dataset.filtro;
    botoesFiltro.forEach((outro) => {
      const ativo = outro === botao;
      outro.classList.toggle('active', ativo);
      outro.setAttribute('aria-pressed', String(ativo));
    });
    renderizarProdutos();
  });
});

// Links do menu que apontam para um filtro (ex.: Autopropelidos)
document.querySelectorAll('[data-filtro-link]').forEach((link) => {
  link.addEventListener('click', () => {
    const alvo = document.querySelector(`.filtro-btn[data-filtro="${link.dataset.filtroLink}"]`);
    if (alvo) {
      alvo.click();
    }
  });
});

formBusca.addEventListener('submit', (evento) => {
  evento.preventDefault();
  termoBusca = campoBusca.value;
  renderizarProdutos();
  document.getElementById('catalogo').scrollIntoView({ behavior: 'smooth' });
});

listaProdutos.addEventListener('click', (evento) => {
  const botao = evento.target.closest('.js-adicionar');
  if (!botao) {
    return;
  }
  const produto = produtosDemo.find((item) => item.id === Number(botao.dataset.id));
  itensNoCarrinho += 1;
  atualizarSeloCarrinho();
  toastTexto.textContent = `${produto.name} foi adicionado ao carrinho (demonstração).`;
  bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2500 }).show();
});

montarFiltrosDinamicos();
renderizarProdutos();
atualizarSeloCarrinho();
