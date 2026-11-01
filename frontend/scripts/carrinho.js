// EletricWay - pagina do carrinho.
// ATENCAO: JS PROVISORIO DE DEMONSTRACAO.
// Os itens abaixo sao ficticios: cada um representa uma linha de `cart_items`
// (quantity) com os dados do produto relacionado (`products`).
// Troque `itensDemo` pelo retorno da API quando for integrar.

let itensDemo = [
  {
    id: 1, // cart_items.id
    cart_id: 1,
    product_id: 1,
    quantity: 1,
    product: {
      name: 'Patinete Urban Sprint',
      type: 'autopropelido',
      image_url: '',
      price: 2890.0,
      stock: 12,
      range_km: 25,
      top_speed_kmh: 25,
    },
  },
  {
    id: 2,
    cart_id: 1,
    product_id: 3,
    quantity: 1,
    product: {
      name: 'Scooter City 1000',
      type: 'ciclomotor',
      image_url: '',
      price: 12900.0,
      stock: 3,
      range_km: 70,
      top_speed_kmh: 45,
    },
  },
];

const FRETE_DEMO = 89.9; // valor fixo so para demonstrar o calculo

let valorFrete = null; // null = ainda nao calculado

const listaItens = document.getElementById('listaItens');
const areaCarrinho = document.getElementById('areaCarrinho');
const carrinhoVazio = document.getElementById('carrinhoVazio');
const resumoQuantidade = document.getElementById('resumoQuantidade');
const seloCarrinho = document.getElementById('seloCarrinho');
const campoCep = document.getElementById('campoCepFrete');
const textoFrete = document.getElementById('textoFrete');
const toastEl = document.getElementById('toastCarrinho');
const toastTexto = document.getElementById('toastCarrinhoTexto');

function avisar(mensagem) {
  toastTexto.textContent = mensagem;
  bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2800 }).show();
}

function criarLinhaItem(item) {
  const { product } = item;
  const subtotal = product.price * item.quantity;
  const imagem = product.image_url
    ? `<img src="${product.image_url}" alt="${product.name}" loading="lazy">`
    : '<i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>';

  return `
    <li class="list-group-item p-3 p-md-4">
      <div class="d-flex gap-3">
        <div class="carrinho-img">${imagem}</div>
        <div class="flex-grow-1">
          <div class="d-flex flex-wrap justify-content-between gap-2">
            <div>
              <h2 class="h6 fw-bold mb-1">${product.name}</h2>
              <p class="small text-secondary mb-2">
                ${ROTULO_TIPO[product.type]} · ${product.range_km} km de autonomia · ${product.top_speed_kmh} km/h
              </p>
            </div>
            <strong class="fs-5">${formatarMoeda(subtotal)}</strong>
          </div>
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3">
              <div class="qtd-controle" role="group" aria-label="Quantidade de ${product.name}">
                <button type="button" class="js-diminuir" data-id="${item.id}" aria-label="Diminuir quantidade" ${item.quantity <= 1 ? 'disabled' : ''}><i class="bi bi-dash-lg"></i></button>
                <span aria-live="polite">${item.quantity}</span>
                <button type="button" class="js-aumentar" data-id="${item.id}" aria-label="Aumentar quantidade" ${item.quantity >= product.stock ? 'disabled' : ''}><i class="bi bi-plus-lg"></i></button>
              </div>
              <span class="small text-secondary">${formatarMoeda(product.price)} cada</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger js-remover" data-id="${item.id}">
              <i class="bi bi-trash me-1" aria-hidden="true"></i>Remover
            </button>
          </div>
          ${item.quantity >= product.stock ? '<p class="small text-warning-emphasis mt-2 mb-0">Quantidade máxima disponível em estoque.</p>' : ''}
        </div>
      </div>
    </li>`;
}

function renderizarCarrinho() {
  const totalUnidades = itensDemo.reduce((soma, item) => soma + item.quantity, 0);
  const subtotal = itensDemo.reduce((soma, item) => soma + item.product.price * item.quantity, 0);
  const vazio = itensDemo.length === 0;

  areaCarrinho.classList.toggle('d-none', vazio);
  carrinhoVazio.classList.toggle('d-none', !vazio);
  seloCarrinho.textContent = totalUnidades;
  seloCarrinho.classList.toggle('d-none', vazio);
  resumoQuantidade.textContent = vazio
    ? ''
    : `${totalUnidades} ${totalUnidades === 1 ? 'item' : 'itens'} no carrinho`;

  listaItens.innerHTML = itensDemo.map(criarLinhaItem).join('');

  document.getElementById('valorSubtotal').textContent = formatarMoeda(subtotal);
  document.getElementById('valorFrete').textContent = valorFrete === null ? '—' : formatarMoeda(valorFrete);
  document.getElementById('valorTotal').textContent = formatarMoeda(subtotal + (valorFrete || 0));
}

listaItens.addEventListener('click', (evento) => {
  const botao = evento.target.closest('button[data-id]');
  if (!botao) {
    return;
  }
  const item = itensDemo.find((linha) => linha.id === Number(botao.dataset.id));

  if (botao.classList.contains('js-aumentar') && item.quantity < item.product.stock) {
    item.quantity += 1;
  } else if (botao.classList.contains('js-diminuir') && item.quantity > 1) {
    item.quantity -= 1;
  } else if (botao.classList.contains('js-remover')) {
    itensDemo = itensDemo.filter((linha) => linha.id !== item.id);
  }
  renderizarCarrinho();
});

campoCep.addEventListener('input', (evento) => {
  evento.target.value = formatarCep(evento.target.value);
});

document.getElementById('formFrete').addEventListener('submit', (evento) => {
  evento.preventDefault();
  const digitos = campoCep.value.replace(/\D/g, '');

  if (digitos.length !== 8) {
    valorFrete = null;
    textoFrete.textContent = 'Digite um CEP válido com 8 números.';
    campoCep.classList.add('is-invalid');
  } else {
    valorFrete = FRETE_DEMO;
    textoFrete.textContent = 'Frete de demonstração para o CEP informado.';
    campoCep.classList.remove('is-invalid');
  }
  renderizarCarrinho();
});

document.getElementById('btnFinalizar').addEventListener('click', () => {
  if (valorFrete === null) {
    avisar('Calcule o frete pelo CEP antes de finalizar.');
    campoCep.focus();
    return;
  }
  avisar('Aqui entra a criação do pedido quando a API estiver ligada.');
});

renderizarCarrinho();
