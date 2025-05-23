document.addEventListener('DOMContentLoaded', () => {
  /* ─────────── Carrito ─────────── */
  const cartCountEl = document.getElementById('cartCount');
  let   cart        = JSON.parse(localStorage.getItem('iaCart') || '[]');
  if (cartCountEl) cartCountEl.textContent = cart.length;

  window.addToCart = item => {
    cart.push(item);
    localStorage.setItem('iaCart', JSON.stringify(cart));
    if (cartCountEl) cartCountEl.textContent = cart.length;
  };

  /* ─────────── Carga demo de catálogos ─────────── */
  const cats = ['memes','arte','literatura'];
  cats.forEach(cat => {
    const grid = document.getElementById(cat + 'Grid');
    if (!grid) return;
    for (let i = 1; i <= 8; i++) {
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <img src="/css/placeholder/${cat}-${i}.jpg" alt="${cat} ${i}">
        <div class="card-body">
          <h3>${cat.charAt(0).toUpperCase() + cat.slice(1)} #${i}</h3>
          <button onclick="addToCart('${cat} #${i}')">Agregar al carrito</button>
        </div>`;
      grid.appendChild(card);
    }
  });

  /* ─────────── Buscador ─────────── */
  const searchEl = document.getElementById('searchInput');
  if (searchEl) {
    searchEl.addEventListener('input', e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('.card').forEach(card => {
        card.style.display = 
          card.querySelector('h3').textContent.toLowerCase().includes(q)
            ? '' : 'none';
      });
    });
  }

  /* ─────────── Pestañas ─────────── */
  const tabs = document.querySelectorAll('.tab-btn');
  if (tabs.length) {
    tabs.forEach(btn => btn.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      cats.forEach(cat => {
        const section = document.getElementById(cat);
        if (section) section.classList.toggle('active', cat === btn.dataset.cat);
      });
    }));

    const firstActive = document.querySelector('.tab-btn.active');
    if (firstActive) firstActive.click();
  }
});
