/* ====== ÍCONES SVG POR CATEGORIA ====== */
const icons = {
  carros:`<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 40v-8l6-10c1-2 3-3 5-3h26c2 0 4 1 5 3l9 10v8"/><path d="M4 40h56"/><circle cx="17" cy="44" r="6"/><circle cx="47" cy="44" r="6"/><path d="M20 22l-4 10h32l-6-10"/></svg>`,
  autopropelidos:`<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="14" cy="50" r="6"/><circle cx="50" cy="50" r="6"/><path d="M14 50 22 14h10"/><path d="M18 36h26l6 14"/><path d="M42 14l8 36"/></svg>`,
  ciclomotores:`<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="14" cy="46" r="8"/><circle cx="50" cy="46" r="8"/><path d="M14 46l10-18h14l6 18"/><path d="M38 28l4-12h8"/><path d="M24 28h-8"/></svg>`
};
const catNames = {carros:'Carro Elétrico', autopropelidos:'Autopropelido', ciclomotores:'Ciclomotor'};

/* ====== PRODUTOS ====== */
const products = [
  {id:1, cat:'carros', name:'Aurora E1 Hatch', price:139900, range:'320 km', speed:'150 km/h', badge:'Mais vendido'},
  {id:2, cat:'carros', name:'Aurora S Sedan', price:219900, range:'480 km', speed:'190 km/h', badge:'Premium'},
  {id:3, cat:'carros', name:'Nova X SUV', price:289900, range:'520 km', speed:'200 km/h', badge:'Novo'},
  {id:4, cat:'carros', name:'Urban Mini City', price:99900, range:'240 km', speed:'120 km/h', badge:'Econômico'},
  {id:5, cat:'autopropelidos', name:'Patinete Spark 350', price:249900/10, range:'30 km', speed:'25 km/h', badge:'Leve'},
  {id:6, cat:'autopropelidos', name:'Patinete Spark Pro', price:429900/10, range:'55 km', speed:'32 km/h', badge:'Pro'},
  {id:7, cat:'autopropelidos', name:'Bike Elétrica Trail', price:689900/10, range:'80 km', speed:'32 km/h', badge:'Off-road'},
  {id:8, cat:'autopropelidos', name:'Bike Dobrável Fold', price:459900/10, range:'60 km', speed:'25 km/h', badge:'Dobrável'},
  {id:9, cat:'ciclomotores', name:'Scooter Pulse 50', price:1290000/10, range:'70 km', speed:'45 km/h', badge:'Urbana'},
  {id:10, cat:'ciclomotores', name:'Scooter Pulse Max', price:1690000/10, range:'110 km', speed:'50 km/h', badge:'Mais vendido'},
  {id:11, cat:'ciclomotores', name:'Moto Volt Retrô', price:1890000/10, range:'95 km', speed:'50 km/h', badge:'Retrô'},
  {id:12, cat:'ciclomotores', name:'Cargo Delivery E', price:1990000/10, range:'120 km', speed:'45 km/h', badge:'Entregas'}
];

const brl = v => v.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
const $ = s => document.querySelector(s);
const grid = $('#grid');

/* ====== RENDER LOJA ====== */
let currentFilter = 'todos';
let currentQuery = '';

/* Remove acentos e deixa minúsculo para a busca ignorar "é" vs "e" */
const norm = s => s.normalize('NFD').replace(/[̀-ͯ]/g,'').toLowerCase();

function render(animate=true){
  const q = norm(currentQuery.trim());
  const list = products.filter(p=>{
    const inCat = currentFilter==='todos' || p.cat===currentFilter;
    const inText = !q || norm(`${p.name} ${catNames[p.cat]} ${p.badge} ${p.range} ${p.speed}`).includes(q);
    return inCat && inText;
  });
  $('#noResults').classList.toggle('d-none', list.length>0);
  grid.innerHTML = list.map((p,i)=>`
    <div class="col-sm-6 col-lg-4 col-xl-3">
      <article class="card product h-100" style="${animate ? `animation-delay:${i*60}ms` : 'animation:none'}">
        <div class="thumb position-relative d-flex align-items-center justify-content-center">
          ${icons[p.cat]}
          <span class="badge rounded-pill badge-neon position-absolute top-0 start-0 m-3 text-uppercase">${p.badge}</span>
        </div>
        <div class="card-body d-flex flex-column gap-2">
          <small style="color:var(--blue)">${catNames[p.cat]}</small>
          <h3 class="h6 fw-bold mb-0">${p.name}</h3>
          <div class="specs d-flex gap-3 small text-body-secondary"><span>${p.range}</span><span>${p.speed}</span></div>
          <div class="mt-auto pt-2 d-flex align-items-center justify-content-between gap-2">
            <div class="fs-5 fw-bold lh-1">${brl(p.price)}<small class="d-block text-body-secondary fw-normal" style="font-size:.72rem">ou 12x de ${brl(p.price/12)}</small></div>
            <button class="add btn btn-grad btn-sm" data-id="${p.id}">Adicionar</button>
          </div>
        </div>
      </article>
    </div>`).join('');
}

function setFilter(f){
  currentFilter = f;
  document.querySelectorAll('.filter').forEach(b=>b.classList.toggle('active',b.dataset.filter===f));
  render();
}

/* Barra de pesquisa: filtra enquanto digita, junto com a categoria escolhida */
$('#search').addEventListener('input',e=>{
  currentQuery = e.target.value;
  render(false);
});

$('#filters').addEventListener('click',e=>{
  const b=e.target.closest('.filter'); if(b) setFilter(b.dataset.filter);
});
document.querySelectorAll('.cat').forEach(c=>c.addEventListener('click',()=>{
  setFilter(c.dataset.go);
  document.getElementById('loja').scrollIntoView({behavior:'smooth'});
}));

/* ====== CARRINHO ====== */
let cart = JSON.parse(localStorage.getItem('voltahead-cart')||'[]');
const save = () => localStorage.setItem('voltahead-cart',JSON.stringify(cart));

function renderCart(){
  const box = $('#items');
  if(!cart.length){
    box.innerHTML='<p class="text-center text-body-secondary mt-5">Seu carrinho está vazio.<br>O futuro está esperando por você ⚡</p>';
  } else {
    box.innerHTML = cart.map(c=>{
      const p = products.find(x=>x.id===c.id);
      return `<div class="card"><div class="card-body d-flex align-items-center gap-3 p-2">
        <div class="mini rounded-3 d-flex align-items-center justify-content-center">${icons[p.cat]}</div>
        <div class="flex-grow-1 small">
          <strong>${p.name}</strong><br><span class="text-body-secondary">${brl(p.price)}</span>
          <div class="btn-group btn-group-sm d-flex align-items-center mt-1" style="width:fit-content">
            <button class="btn btn-outline-secondary" data-act="dec" data-id="${p.id}">−</button>
            <span class="px-2">${c.qty}</span>
            <button class="btn btn-outline-secondary" data-act="inc" data-id="${p.id}">+</button>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-danger" data-act="rm" data-id="${p.id}" aria-label="Remover">🗑</button>
      </div></div>`;}).join('');
  }
  const total = cart.reduce((s,c)=>s+products.find(x=>x.id===c.id).price*c.qty,0);
  $('#total').textContent = brl(total);
  $('#cartCount').textContent = cart.reduce((s,c)=>s+c.qty,0);
  save();
}

/* Toast do Bootstrap */
function toast(text){
  $('#toastMsg').textContent = text;
  bootstrap.Toast.getOrCreateInstance($('#toast'),{delay:2000}).show();
}

grid.addEventListener('click',e=>{
  const b=e.target.closest('.add'); if(!b) return;
  const id=+b.dataset.id, item=cart.find(c=>c.id===id);
  item ? item.qty++ : cart.push({id,qty:1});
  renderCart();
  toast('Adicionado ao carrinho ⚡');
});

$('#items').addEventListener('click',e=>{
  const b=e.target.closest('button[data-act]'); if(!b) return;
  const id=+b.dataset.id, item=cart.find(c=>c.id===id);
  if(b.dataset.act==='inc') item.qty++;
  if(b.dataset.act==='dec') item.qty--;
  if(b.dataset.act==='rm' || item.qty<=0) cart = cart.filter(c=>c.id!==id);
  renderCart();
});

/* Abrir/fechar o carrinho (Offcanvas) é feito pelo Bootstrap via data-bs-*; aqui só fechamos ao finalizar */
$('#checkout').addEventListener('click',()=>{
  if(!cart.length){toast('Seu carrinho está vazio');return;}
  const drawerEl = $('#cartDrawer');

  // Para finalizar é preciso estar logado: abre a janela de login
  if(!user){
    bootstrap.Offcanvas.getOrCreateInstance(drawerEl).hide();
    toast('Entre na sua conta para finalizar a compra');
    setTimeout(()=>bootstrap.Modal.getOrCreateInstance($('#authModal')).show(), 350);
    return;
  }

  // TODO: trocar por POST /api/orders no Laravel. Por enquanto o pedido fica salvo no navegador.
  const order = {
    id: 'VA-' + Date.now().toString().slice(-6),
    email: user.email,
    date: new Date().toISOString(),
    status: 'Em preparação',
    items: cart.map(c=>{ const p = products.find(x=>x.id===c.id); return {name:p.name, price:p.price, qty:c.qty}; }),
    total: cart.reduce((s,c)=>s+products.find(x=>x.id===c.id).price*c.qty,0)
  };
  const orders = JSON.parse(localStorage.getItem('voltahead-orders') || '[]');
  orders.push(order);
  localStorage.setItem('voltahead-orders', JSON.stringify(orders));

  toast(`Pedido ${order.id} realizado! Veja em Informações da conta 🚀`);
  cart=[]; renderCart();
  bootstrap.Offcanvas.getOrCreateInstance(drawerEl).hide();
});

/* ====== MENU MOBILE: fecha ao clicar num link ====== */
document.querySelectorAll('#nav .nav-link').forEach(a=>a.addEventListener('click',()=>{
  bootstrap.Collapse.getOrCreateInstance($('#nav'),{toggle:false}).hide();
}));

/* ====== NEWSLETTER ====== */
$('#newsletter').addEventListener('submit',e=>{
  e.preventDefault();
  $('#msg').textContent='Cupom VOLT10 enviado para '+$('#email').value+' ⚡';
  e.target.reset();
});

/* ====== CONTADORES ANIMADOS ====== */
function animateCounters(){
  document.querySelectorAll('[data-count]').forEach(el=>{
    const end=+el.dataset.count, suf=el.dataset.suffix||'', dur=1600, t0=performance.now();
    (function step(t){
      const p=Math.min((t-t0)/dur,1);
      el.textContent=Math.round(end*p)+suf;
      if(p<1) requestAnimationFrame(step);
    })(t0);
  });
}

/* ====== LOGIN / CADASTRO (simulado) ======
   Por enquanto só guarda nome e e-mail no navegador, SEM senha.
   Quando o back-end Laravel estiver pronto, troque os trechos marcados
   com TODO por chamadas à API (ex.: POST /api/login e POST /api/register). */
let user = JSON.parse(localStorage.getItem('voltahead-user') || 'null');
let authMode = 'login'; // 'login' ou 'register'

function renderUser(){
  $('#loginBtn').classList.toggle('d-none', !!user);
  $('#userMenu').classList.toggle('d-none', !user);
  if(user){
    $('#userName').textContent = user.name.split(' ')[0];
    // Mostra a foto do perfil no botão, se houver; senão o ícone padrão
    $('#navAvatar').classList.toggle('d-none', !user.photo);
    $('#navIcon').classList.toggle('d-none', !!user.photo);
    if(user.photo) $('#navAvatar').src = user.photo;
  }
}

function setAuthMode(mode){
  authMode = mode;
  const reg = mode === 'register';
  $('#nameField').classList.toggle('d-none', !reg);
  $('#authName').required = reg;
  $('#authTitle').textContent = reg ? 'Criar sua conta' : 'Entrar na sua conta';
  $('#authSubtitle').textContent = reg ? 'Leva menos de um minuto.' : 'Acesse seus pedidos e favoritos.';
  $('#authSubmit').textContent = reg ? 'Criar conta' : 'Entrar';
  $('#authSwitchText').textContent = reg ? 'Já tem conta?' : 'Ainda não tem conta?';
  $('#authSwitch').textContent = reg ? 'Entrar' : 'Criar conta';
  $('#authPass').autocomplete = reg ? 'new-password' : 'current-password';
  $('#authError').classList.add('d-none');
}

function showAuthError(msg){
  const el = $('#authError');
  el.textContent = msg;
  el.classList.remove('d-none');
}

$('#authSwitch').addEventListener('click',()=>setAuthMode(authMode==='login' ? 'register' : 'login'));

// Sempre abre o modal no modo "entrar" e com o formulário limpo
$('#authModal').addEventListener('show.bs.modal',()=>{ $('#authForm').reset(); setAuthMode('login'); });

$('#authForm').addEventListener('submit',e=>{
  e.preventDefault();
  const email = $('#authEmail').value.trim();
  const pass = $('#authPass').value;
  const name = $('#authName').value.trim();

  if(authMode==='register' && name.length<2) return showAuthError('Informe seu nome.');
  if(!/^\S+@\S+\.\S+$/.test(email)) return showAuthError('Digite um e-mail válido.');
  if(pass.length<6) return showAuthError('A senha precisa ter pelo menos 6 caracteres.');

  // TODO: aqui entra a chamada à API do Laravel (fetch POST) com email e senha.
  // Perfis ficam guardados por e-mail para manter foto, telefone etc. quando a pessoa sair e entrar de novo
  const profiles = JSON.parse(localStorage.getItem('voltahead-profiles') || '{}');
  const saved = profiles[email] || {};
  user = {
    createdAt: new Date().toISOString(),
    ...saved,
    name: authMode==='register' ? name : (saved.name || email.split('@')[0]),
    email
  };
  profiles[email] = user;
  localStorage.setItem('voltahead-profiles', JSON.stringify(profiles));
  localStorage.setItem('voltahead-user', JSON.stringify(user));

  renderUser();
  bootstrap.Modal.getInstance($('#authModal')).hide();
  toast(`Bem-vindo, ${user.name.split(' ')[0]} ⚡`);
});

$('#logoutBtn').addEventListener('click',()=>{
  user = null;
  localStorage.removeItem('voltahead-user');
  renderUser();
  toast('Você saiu da conta');
});

/* ====== INIT ====== */
renderUser();
$('#year').textContent=new Date().getFullYear();
render(); renderCart(); animateCounters();