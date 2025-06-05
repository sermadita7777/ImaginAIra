// cookies.js

document.addEventListener('DOMContentLoaded', () => {
  const banner = document.getElementById('cookie-banner');
  const overlay = document.getElementById('cookie-overlay');
  const settings = document.getElementById('cookie-settings');
  const acceptBtn = document.getElementById('acceptAllCookies');
  const customizeBtn = document.getElementById('customizeCookies');
  const form = document.getElementById('cookieForm');

  if (!getCookie('cookieConsent')) {
    banner.classList.remove('hidden');
    overlay.classList.remove('hidden');
  }

  acceptBtn.addEventListener('click', () => {
    const consent = {
      necessary: true,
      analytics: true,
      personalization: true,
      marketing: true
    };
    saveConsent(consent);
  });

  customizeBtn.addEventListener('click', () => {
    settings.classList.toggle('show');
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const consent = {
      necessary: true,
      analytics: form.analytics.checked,
      personalization: form.personalization.checked,
      marketing: form.marketing.checked
    };
    saveConsent(consent);
  });

  function generateVisitorId() {
    // Verificar si ya existe un ID de visitante
    let visitorId = getCookie('visitor_id');
    
    // Si no existe, crear uno nuevo
    if (!visitorId) {
      visitorId = 'v_' + Math.random().toString(36).substring(2, 15) + 
                 Math.random().toString(36).substring(2, 15);
      document.cookie = `visitor_id=${visitorId}; path=/; max-age=${60 * 60 * 24 * 365}`;
    }
    
    return visitorId;
  }

  function saveConsent(consent) {
    // Guardar en cookie local
    document.cookie = `cookieConsent=${JSON.stringify(consent)}; path=/; max-age=${60 * 60 * 24 * 365}`;
    banner.classList.add('hidden');
    overlay.classList.add('hidden');
    
    // Obtener ID de visitante
    const visitorId = generateVisitorId();
    
    // Enviar a servidor
    fetch('guardar_consentimiento.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        consent: consent,
        visitor_id: visitorId
      })
    });
  }

  function getCookie(name) {
    let cookieArr = document.cookie.split(';');
    for (let cookie of cookieArr) {
      let c = cookie.trim();
      if (c.startsWith(name + '=')) {
        return c.substring(name.length + 1);
      }
    }
    return null;
  }
  
  // Verificar consentimiento guardado
  const visitorId = getCookie('visitor_id');
  if (visitorId && !getCookie('cookieConsent')) {
    // Si hay ID de visitante pero no hay cookie de consentimiento local,
    // intentar recuperar del servidor
    fetch(`verificar_consentimiento.php?visitor_id=${encodeURIComponent(visitorId)}`)
      .then(response => response.json())
      .then(data => {
        if (data.consent) {
          // Restaurar consentimiento desde el servidor
          document.cookie = `cookieConsent=${JSON.stringify(data.consent)}; path=/; max-age=${60 * 60 * 24 * 365}`;
        } else {
          // No hay consentimiento guardado, mostrar banner
          banner.classList.remove('hidden');
          overlay.classList.remove('hidden');
        }
      })
      .catch(() => {
        // Error al verificar, mostrar banner
        banner.classList.remove('hidden');
        overlay.classList.remove('hidden');
      });
  } else if (!getCookie('cookieConsent')) {
    // No hay ID de visitante ni consentimiento, mostrar banner
    banner.classList.remove('hidden');
    overlay.classList.remove('hidden');
  }
});
