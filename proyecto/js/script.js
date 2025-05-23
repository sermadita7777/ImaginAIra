document.addEventListener('DOMContentLoaded', () => {
  // Navegación según sesión
  const user = localStorage.getItem('currentUser');
  document.getElementById('authNav').classList.toggle('hidden', !!user);
  document.getElementById('userNav').classList.toggle('hidden', !user);

  // Carrito
  let cart = JSON.parse(localStorage.getItem('iaCart') || '[]');
  document.getElementById('cartCount').textContent = cart.length;
  window.addToCart = item => {
    cart.push(item);
    localStorage.setItem('iaCart', JSON.stringify(cart));
    document.getElementById('cartCount').textContent = cart.length;
  };

  // Carga demo de catálogos
  const cats = ['memes','arte','literatura'];
  cats.forEach(cat => {
    const grid = document.getElementById(cat + 'Grid');
    for(let i=1; i<=8; i++){
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <img src="css/placeholder/${cat}-${i}.jpg" alt="${cat} ${i}">
        <div class="card-body">
          <h3>${cat.charAt(0).toUpperCase()+cat.slice(1)} #${i}</h3>
          <button onclick="addToCart('${cat} #${i}')">Agregar al carrito</button>
        </div>`;
      grid.appendChild(card);
    }
  });

  // Búsqueda en todas las tarjetas
  document.getElementById('searchInput')
    .addEventListener('input', e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('.card').forEach(card => {
        const txt = card.querySelector('h3').textContent.toLowerCase();
        card.style.display = txt.includes(q)? '' : 'none';
      });
    });

  // Lógica de pestañas
  const tabs = document.querySelectorAll('.tab-btn');
  tabs.forEach(btn => btn.addEventListener('click', () => {
    tabs.forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    cats.forEach(cat => {
      document.getElementById(cat)
        .classList.toggle('active', cat === btn.dataset.cat);
    });
  }));

  // Activa la primera pestaña
  document.querySelector('.tab-btn.active').click();
});
