// EletricWay - pagina da conta do usuario.
// ATENCAO: JS PROVISORIO DE DEMONSTRACAO.
// Os dados abaixo sao ficticios e seguem os campos das tabelas
// `users`, `addresses`, `orders` e `order_items`. Troque pelas chamadas de API depois.

const usuarioDemo = {
  id: 1,
  name: 'Maria Souza',
  email: 'maria.souza@exemplo.com',
  role: 'user', // 'user' | 'admin'
  created_at: '2026-03-14T10:20:00',
};

let enderecosDemo = [
  {
    id: 1,
    user_id: 1,
    recipient: 'Maria Souza',
    cep: '85010-000',
    street: 'Rua das Palmeiras',
    number: '120',
    complement: 'Apto 31',
    district: 'Centro',
    city: 'Guarapuava',
    state: 'PR',
  },
  {
    id: 2,
    user_id: 1,
    recipient: 'João Souza',
    cep: '80010-010',
    street: 'Avenida Central',
    number: '845',
    complement: '',
    district: 'Batel',
    city: 'Curitiba',
    state: 'PR',
  },
];

const pedidosDemo = [
  {
    id: 1063,
    user_id: 1,
    status: 'pendente', // 'pendente' | 'pago' | 'enviado' | 'entregue' | 'cancelado'
    subtotal_cents: 2890.0,
    shipping_cents: 89.9,
    total_cents: 2979.9,
    ship_recipient: 'Maria Souza',
    ship_cep: '85010-000',
    ship_street: 'Rua das Palmeiras',
    ship_number: '120',
    ship_complement: 'Apto 31',
    ship_district: 'Centro',
    ship_city: 'Guarapuava',
    ship_state: 'PR',
    paid_at: null,
    shipped_at: null,
    delivered_at: null,
    cancelled_at: null,
    created_at: '2026-10-28T14:05:00',
    items: [
      { id: 1, product_id: 1, product_name: 'Patinete Urban Sprint', unit_price: 2890.0, quantity: 1, subtotal_cents: 2890.0 },
    ],
  },
  {
    id: 1057,
    user_id: 1,
    status: 'enviado',
    subtotal_cents: 6490.0,
    shipping_cents: 120.0,
    total_cents: 6610.0,
    ship_recipient: 'João Souza',
    ship_cep: '80010-010',
    ship_street: 'Avenida Central',
    ship_number: '845',
    ship_complement: '',
    ship_district: 'Batel',
    ship_city: 'Curitiba',
    ship_state: 'PR',
    paid_at: '2026-10-20T09:12:00',
    shipped_at: '2026-10-22T16:40:00',
    delivered_at: null,
    cancelled_at: null,
    created_at: '2026-10-20T09:00:00',
    items: [
      { id: 2, product_id: 2, product_name: 'Bike Elétrica Trilha 500', unit_price: 6490.0, quantity: 1, subtotal_cents: 6490.0 },
    ],
  },
  {
    id: 1042,
    user_id: 1,
    status: 'entregue',
    subtotal_cents: 8780.0,
    shipping_cents: 150.0,
    total_cents: 8930.0,
    ship_recipient: 'Maria Souza',
    ship_cep: '85010-000',
    ship_street: 'Rua das Palmeiras',
    ship_number: '120',
    ship_complement: 'Apto 31',
    ship_district: 'Centro',
    ship_city: 'Guarapuava',
    ship_state: 'PR',
    paid_at: '2026-09-02T11:30:00',
    shipped_at: '2026-09-04T08:15:00',
    delivered_at: '2026-09-09T15:50:00',
    cancelled_at: null,
    created_at: '2026-09-02T11:20:00',
    items: [
      { id: 3, product_id: 5, product_name: 'Patinete Max Cruiser', unit_price: 4390.0, quantity: 2, subtotal_cents: 8780.0 },
    ],
  },
];

const STATUS_PEDIDO = {
  pendente: { rotulo: 'Pendente', classe: 'text-bg-warning' },
  pago: { rotulo: 'Pago', classe: 'text-bg-info' },
  enviado: { rotulo: 'Enviado', classe: 'text-bg-primary' },
  entregue: { rotulo: 'Entregue', classe: 'text-bg-success' },
  cancelado: { rotulo: 'Cancelado', classe: 'text-bg-danger' },
};

const ROTULO_PAPEL = { user: 'Cliente', admin: 'Administrador' };

const toastEl = document.getElementById('toastConta');
const toastTexto = document.getElementById('toastContaTexto');
const modalEnderecoEl = document.getElementById('modalEndereco');
const formEndereco = document.getElementById('formEndereco');

function avisar(mensagem) {
  toastTexto.textContent = mensagem;
  bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2500 }).show();
}

/* ---------- Dados pessoais ---------- */
function renderizarUsuario() {
  const iniciais = usuarioDemo.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((parte) => parte[0].toUpperCase())
    .join('');

  document.getElementById('avatarIniciais').textContent = iniciais;
  document.getElementById('resumoNome').textContent = usuarioDemo.name;
  document.getElementById('resumoEmail').textContent = usuarioDemo.email;
  document.getElementById('resumoPapel').textContent = ROTULO_PAPEL[usuarioDemo.role];
  document.getElementById('campoNome').value = usuarioDemo.name;
  document.getElementById('campoEmail').value = usuarioDemo.email;
  document.getElementById('campoPapel').value = ROTULO_PAPEL[usuarioDemo.role];
  document.getElementById('campoMembro').value = formatarData(usuarioDemo.created_at);
}

document.getElementById('formDados').addEventListener('submit', (evento) => {
  evento.preventDefault();
  if (!evento.target.checkValidity()) {
    evento.target.reportValidity();
    return;
  }
  usuarioDemo.name = document.getElementById('campoNome').value.trim();
  usuarioDemo.email = document.getElementById('campoEmail').value.trim();
  renderizarUsuario();
  avisar('Dados atualizados (demonstração).');
});

document.getElementById('btnSair').addEventListener('click', () => {
  avisar('Aqui entra o logout quando a API estiver ligada.');
});

/* ---------- Enderecos ---------- */
function criarCardEndereco(endereco) {
  const complemento = endereco.complement ? ` - ${endereco.complement}` : '';
  return `
    <div class="col-md-6">
      <article class="endereco-card h-100 p-3 d-flex flex-column gap-2">
        <h3 class="h6 fw-bold mb-0"><i class="bi bi-geo-alt-fill text-success me-1" aria-hidden="true"></i>${endereco.recipient}</h3>
        <p class="small text-secondary mb-0">
          ${endereco.street}, ${endereco.number}${complemento}<br>
          ${endereco.district} · ${endereco.city}/${endereco.state}<br>
          CEP ${endereco.cep}
        </p>
        <div class="mt-auto d-flex gap-2 pt-2">
          <button type="button" class="btn btn-sm btn-outline-ew js-editar-endereco" data-id="${endereco.id}"><i class="bi bi-pencil me-1"></i>Editar</button>
          <button type="button" class="btn btn-sm btn-outline-danger js-remover-endereco" data-id="${endereco.id}"><i class="bi bi-trash me-1"></i>Remover</button>
        </div>
      </article>
    </div>`;
}

function renderizarEnderecos() {
  document.getElementById('listaEnderecos').innerHTML = enderecosDemo.map(criarCardEndereco).join('');
  document.getElementById('semEnderecos').classList.toggle('d-none', enderecosDemo.length > 0);
}

function abrirModalEndereco(endereco) {
  formEndereco.reset();
  document.getElementById('tituloModalEndereco').textContent = endereco ? 'Editar endereço' : 'Novo endereço';
  document.getElementById('enderecoId').value = endereco ? endereco.id : '';
  if (endereco) {
    document.getElementById('enderecoDestinatario').value = endereco.recipient;
    document.getElementById('enderecoCep').value = endereco.cep;
    document.getElementById('enderecoRua').value = endereco.street;
    document.getElementById('enderecoNumero').value = endereco.number;
    document.getElementById('enderecoComplemento').value = endereco.complement || '';
    document.getElementById('enderecoBairro').value = endereco.district;
    document.getElementById('enderecoCidade').value = endereco.city;
    document.getElementById('enderecoUf').value = endereco.state;
  }
  bootstrap.Modal.getOrCreateInstance(modalEnderecoEl).show();
}

document.getElementById('btnNovoEndereco').addEventListener('click', () => abrirModalEndereco(null));

document.getElementById('enderecoCep').addEventListener('input', (evento) => {
  evento.target.value = formatarCep(evento.target.value);
});

document.getElementById('listaEnderecos').addEventListener('click', (evento) => {
  const botaoEditar = evento.target.closest('.js-editar-endereco');
  const botaoRemover = evento.target.closest('.js-remover-endereco');

  if (botaoEditar) {
    abrirModalEndereco(enderecosDemo.find((item) => item.id === Number(botaoEditar.dataset.id)));
  }
  if (botaoRemover) {
    enderecosDemo = enderecosDemo.filter((item) => item.id !== Number(botaoRemover.dataset.id));
    renderizarEnderecos();
    avisar('Endereço removido (demonstração).');
  }
});

formEndereco.addEventListener('submit', (evento) => {
  evento.preventDefault();
  if (!formEndereco.checkValidity()) {
    formEndereco.reportValidity();
    return;
  }

  const idEditado = Number(document.getElementById('enderecoId').value);
  const dados = {
    recipient: document.getElementById('enderecoDestinatario').value.trim(),
    cep: document.getElementById('enderecoCep').value,
    street: document.getElementById('enderecoRua').value.trim(),
    number: document.getElementById('enderecoNumero').value.trim(),
    complement: document.getElementById('enderecoComplemento').value.trim(),
    district: document.getElementById('enderecoBairro').value.trim(),
    city: document.getElementById('enderecoCidade').value.trim(),
    state: document.getElementById('enderecoUf').value.trim().toUpperCase(),
  };

  if (idEditado) {
    enderecosDemo = enderecosDemo.map((item) => (item.id === idEditado ? { ...item, ...dados } : item));
  } else {
    const proximoId = enderecosDemo.reduce((maior, item) => Math.max(maior, item.id), 0) + 1;
    enderecosDemo.push({ id: proximoId, user_id: usuarioDemo.id, ...dados });
  }

  bootstrap.Modal.getInstance(modalEnderecoEl).hide();
  renderizarEnderecos();
  avisar('Endereço salvo (demonstração).');
});

/* ---------- Pedidos ---------- */
function criarLinhaTempo(pedido) {
  const eventos = [
    { rotulo: 'Pedido criado', data: pedido.created_at },
    { rotulo: 'Pagamento confirmado', data: pedido.paid_at },
    { rotulo: 'Enviado', data: pedido.shipped_at },
    { rotulo: 'Entregue', data: pedido.delivered_at },
    { rotulo: 'Cancelado', data: pedido.cancelled_at },
  ].filter((evento) => evento.data);

  return eventos
    .map((evento) => `<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>${evento.rotulo} · ${formatarDataHora(evento.data)}</span></li>`)
    .join('');
}

function criarItemPedido(pedido, indice) {
  const status = STATUS_PEDIDO[pedido.status];
  const complemento = pedido.ship_complement ? ` - ${pedido.ship_complement}` : '';
  const linhasItens = pedido.items
    .map(
      (item) => `
        <tr>
          <td>${item.product_name}</td>
          <td class="text-end">${formatarMoeda(item.unit_price)}</td>
          <td class="text-center">${item.quantity}</td>
          <td class="text-end">${formatarMoeda(item.subtotal_cents)}</td>
        </tr>`
    )
    .join('');

  return `
    <div class="accordion-item pedido-item">
      <h3 class="accordion-header">
        <button class="accordion-button ${indice === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#pedido-${pedido.id}" aria-expanded="${indice === 0}" aria-controls="pedido-${pedido.id}">
          <span class="d-flex flex-wrap align-items-center gap-2 gap-md-4 w-100 me-3">
            <strong>Pedido #${pedido.id}</strong>
            <span class="text-secondary small">${formatarData(pedido.created_at)}</span>
            <span class="badge ${status.classe}">${status.rotulo}</span>
            <strong class="ms-md-auto">${formatarMoeda(pedido.total_cents)}</strong>
          </span>
        </button>
      </h3>
      <div id="pedido-${pedido.id}" class="accordion-collapse collapse ${indice === 0 ? 'show' : ''}" data-bs-parent="#listaPedidos">
        <div class="accordion-body">
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr><th scope="col">Produto</th><th scope="col" class="text-end">Preço</th><th scope="col" class="text-center">Qtd.</th><th scope="col" class="text-end">Subtotal</th></tr>
              </thead>
              <tbody>${linhasItens}</tbody>
            </table>
          </div>
          <div class="row g-4 mt-1">
            <div class="col-md-4">
              <h4 class="h6 fw-bold">Resumo</h4>
              <dl class="row small mb-0">
                <dt class="col-6 fw-normal text-secondary">Subtotal</dt><dd class="col-6 text-end mb-1">${formatarMoeda(pedido.subtotal_cents)}</dd>
                <dt class="col-6 fw-normal text-secondary">Frete</dt><dd class="col-6 text-end mb-1">${formatarMoeda(pedido.shipping_cents)}</dd>
                <dt class="col-6">Total</dt><dd class="col-6 text-end fw-bold mb-0">${formatarMoeda(pedido.total_cents)}</dd>
              </dl>
            </div>
            <div class="col-md-4">
              <h4 class="h6 fw-bold">Entrega</h4>
              <p class="small text-secondary mb-0">
                ${pedido.ship_recipient}<br>
                ${pedido.ship_street}, ${pedido.ship_number}${complemento}<br>
                ${pedido.ship_district} · ${pedido.ship_city}/${pedido.ship_state}<br>
                CEP ${pedido.ship_cep}
              </p>
            </div>
            <div class="col-md-4">
              <h4 class="h6 fw-bold">Andamento</h4>
              <ul class="linha-tempo">${criarLinhaTempo(pedido)}</ul>
            </div>
          </div>
        </div>
      </div>
    </div>`;
}

function renderizarPedidos() {
  document.getElementById('listaPedidos').innerHTML = pedidosDemo.map(criarItemPedido).join('');
  document.getElementById('semPedidos').classList.toggle('d-none', pedidosDemo.length > 0);
}

/* ---------- Seguranca ---------- */
document.getElementById('formSenha').addEventListener('submit', (evento) => {
  evento.preventDefault();
  const formulario = evento.target;
  const confirmacao = document.getElementById('senhaConfirmar');
  const senhasIguais = document.getElementById('senhaNova').value === confirmacao.value;

  confirmacao.classList.toggle('is-invalid', !senhasIguais);
  if (!formulario.checkValidity()) {
    formulario.reportValidity();
    return;
  }
  if (!senhasIguais) {
    return;
  }
  formulario.reset();
  avisar('Senha atualizada (demonstração).');
});

renderizarUsuario();
renderizarEnderecos();
renderizarPedidos();
