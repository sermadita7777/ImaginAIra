document.addEventListener('DOMContentLoaded', () => {
    const user = localStorage.getItem('currentUser');
    if (!user) return location.href = 'login.html';
  
    fetch(`/api/user/${user}/likes`)
      .then(res => res.json())
      .then(items => {
        const grid = document.getElementById('likesGrid');
        if (!items.length) {
          grid.innerHTML = '<p>No has dado “Me gusta” a ninguna publicación aún.</p>';
          return;
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
                <button disabled><i class="fas fa-heart"></i> ${item.likes}</button>
                <button onclick="location.href='catalog.html'">Ver catálogo</button>
              </div>
            </div>`;
          grid.appendChild(card);
        });
      });
  });
  