import { 
  signIn, 
  signUp, 
  signOut,
  getContentByCategory,
  addToLikes,
  removeLike,
  addToCart,
  removeFromCart,
  getUserLikes,
  getUserCart
} from './supabase.js';

document.addEventListener('DOMContentLoaded', async () => {
  // Auth state
  const session = await supabase.auth.getSession();
  const currentUser = session?.user;

  /* ────── Cart ────── */
  const cartCountEl = document.getElementById('cartCount');
  const updateCartCount = async () => {
    const { data } = await getUserCart();
    if (cartCountEl) cartCountEl.textContent = data?.length || 0;
  };
  
  window.addToCart = async (contentId) => {
    if (!currentUser) return location.href = 'login.html';
    await addToCart(contentId);
    await updateCartCount();
  };

  /* ────── Likes ────── */
  window.likeItem = async (btn, id) => {
    if (!currentUser) return location.href = 'login.html';
    const isLiked = btn.classList.contains('liked');
    
    if (isLiked) {
      await removeLike(id);
    } else {
      await addToLikes(id);
    }
    
    btn.classList.toggle('liked');
    const likesCount = btn.querySelector('span');
    likesCount.textContent = parseInt(likesCount.textContent) + (isLiked ? -1 : 1);
  };

  /* ────── Fetch content ────── */
  let data = { memes: [], arte: [], literatura: [] };
  
  async function fetchCategoryContent(category) {
    const { data: items, error } = await getContentByCategory(category);
    if (!error && items) {
      data[category] = items;
    }
  }

  await Promise.all([
    fetchCategoryContent('memes'),
    fetchCategoryContent('arte'),
    fetchCategoryContent('literatura')
  ]);

  renderCategory('memes');

  /* ────── Render cards ────── */
  function renderCategory(cat) {
    const grid = document.getElementById(cat + 'Grid');
    if (!grid) return;
    
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
        <img src="${item.image_url}" alt="${item.title}">
        <div class="card-body">
          <h3>${item.title}</h3>
          <p>${item.description}</p>
          <div class="card-actions">
            <button onclick="likeItem(this, '${item.id}')"
                    class="${item.liked ? 'liked' : ''}">
              <i class="fas fa-heart"></i> <span>${item.likes_count || 0}</span>
            </button>
            <button onclick="addToCart('${item.id}')">
              <i class="fas fa-cart-plus"></i>
            </button>
          </div>
        </div>`;
      grid.appendChild(card);
    });
  }

  /* ────── Tabs ────── */
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

  /* ────── Global search ────── */
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('.card').forEach(c => {
        c.style.display = c.querySelector('h3').textContent.toLowerCase().includes(q)
          ? '' : 'none';
      });
    });
  }

  /* ────── Literature filters ────── */
  document.querySelectorAll('.lit-filter').forEach(cb => {
    cb.addEventListener('change', () => {
      if (document.querySelector('.tab-btn.active')?.dataset.cat === 'literatura') {
        renderCategory('literatura');
      }
    });
  });

  // Initialize cart count
  await updateCartCount();
});