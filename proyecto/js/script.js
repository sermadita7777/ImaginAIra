document.addEventListener('DOMContentLoaded', () => {
  // Estado de usuario
  const currentUser = localStorage.getItem('currentUser');

  /* ────── Carrito ────── */
  const cartCountEl = document.getElementById('cartCount');
  let cart = JSON.parse(localStorage.getItem('iaCart') || '[]');
  if (cartCountEl) cartCountEl.textContent = cart.length;
  window.addToCart = title => {
    if (!currentUser) return location.href = 'login.html';
    cart.push(title);
    localStorage.setItem('iaCart', JSON.stringify(cart));
    cartCountEl.textContent = cart.length;
    // opcional: POST '/api/cart/add'
  };

  /* ────── Me gusta ────── */
  window.likeItem = (btn, id) => {
    if (!currentUser) return location.href = 'login.html';
    const liked = btn.classList.toggle('liked');
    // enviar al servidor
    fetch('/api/like', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ user: currentUser, postId: id, like: liked })
    });
  };

  /* ────── Fetch real de BD ────── */
  let data = { memes: [], arte: [], literatura: [] };
  fetch('/api/catalogo')
    .then(res => res.json())
    .then(json => data = json)
    .then(() => renderCategory('memes'));

  /* ────── Renderizar tarjetas ────── */
  function renderCategory(cat) {
    const grid = document.getElementById(cat + 'Grid');
    grid.innerHTML = '';
    let items = data[cat] || [];

    if (cat === 'literatura') {
      const checked = Array.from(document.querySelectorAll('.lit-filter:checked'))
        .map(cb => cb.value);
      items = items.filter(item => checked.includes(item.tag));
    }

    items.forEach(item => {
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <img src="${item.img}" alt="${item.title}">
        <div class="card-body">
          <h3>${item.title}</h3>
          <p>${item.desc}</p>
          <div class="card-actions">
            <button onclick="likeItem(this, '${item.id}')"
                    class="${item.liked ? 'liked' : ''}">
              <i class="fas fa-heart"></i> ${item.likes || 0}
            </button>
            <button onclick="addToCart('${item.title}')">
              <i class="fas fa-cart-plus"></i>
            </button>
          </div>
        </div>`;
      grid.appendChild(card);
    });
  }

  /* ────── Pestañas ────── */
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.catalog').forEach(sec => {
        sec.classList.toggle('active', sec.id === btn.dataset.cat);
      });
      renderCategory(btn.dataset.cat);
    });
  });

  /* ────── Búsqueda global ────── */
  document.getElementById('searchInput').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.card').forEach(c => {
      c.style.display = c.querySelector('h3').textContent.toLowerCase().includes(q)
        ? '' : 'none';
    });
  });

  /* ────── Filtros literatura ────── */
  document.querySelectorAll('.lit-filter').forEach(cb=>{
    cb.addEventListener('change', ()=> {
      if (document.querySelector('.tab-btn.active').dataset.cat === 'literatura') {
        renderCategory('literatura');
      }
    });
  });
});
