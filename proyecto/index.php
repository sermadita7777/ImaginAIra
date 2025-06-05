<?php
require 'header.php';

// Consulta para obtener los 2 contenidos con más likes
$stmt = $pdo->query("
  SELECT c.*, COUNT(cl.id_like) AS likes_count
  FROM contenidos c
  LEFT JOIN contenido_likes cl ON cl.contenido_id = c.id_contenido
  GROUP BY c.id_contenido
  ORDER BY likes_count DESC
  LIMIT 2
");

$destacados = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/index.css">
<link
  href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
  rel="stylesheet"
/>

<section class="hero section">
  <div class="container">
    <div class="hero-content">
      <h1>Bienvenido a IA-Lovers</h1>
      <p class="slogan">Tu plataforma para descubrir contenido generado por IA</p>
      <p class="subtitle">
        Explora nuestra colección de memes, arte y literatura creados con
        inteligencia artificial
      </p>
      <div class="hero-buttons">
        <a href="catalog.php" class="btn-primary">
          Explorar Catálogo <i class="fas fa-arrow-right"></i>
        </a>
        <a href="help.php" class="btn-primary">
          ¿Cómo funciona? <i class="fas fa-info-circle"></i>
        </a>
              
        <?php if (!$user): ?>
        <a href="login.php" class="btn-secondary">Iniciar Sesión</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Categorías -->
<section class="categories section">
  <div class="container">
    <div class="section-header">
      <h2>Nuestras Categorías</h2>
      <div class="underline"></div>
    </div>
    <div class="categories-container">
      <?php foreach ([
        ['icon'=>'fas fa-image','title'=>'Memes','desc'=>'Descubre divertidos memes generados por inteligencia artificial','link'=>'catalog.php#memes'],
        ['icon'=>'fas fa-paint-brush','title'=>'Arte','desc'=>'Explora increíbles obras de arte creadas con ayuda de la IA','link'=>'catalog.php#arte'],
        ['icon'=>'fas fa-book','title'=>'Literatura','desc'=>'Sumérgete en historias fascinantes generadas por IA','link'=>'catalog.php#literatura'],
      ] as $cat): ?>
      <div class="category-card">
        <div class="category-icon"><i class="<?= $cat['icon'] ?>"></i></div>
        <h3><?= $cat['title'] ?></h3>
        <p><?= $cat['desc'] ?></p>
        <a href="<?= $cat['link'] ?>" class="category-link">Ver Galería <i class="fas fa-arrow-right"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Destacados -->
<section class="featured section bg-light">
  <div class="container">
    <div class="section-header">
      <h2>Contenido Destacado</h2>
      <div class="underline"></div>
    </div>
    <div class="featured-items">
      <?php if (empty($destacados)): ?>
        <p>No hay contenido destacado aún.</p>
      <?php else: ?>
        <?php foreach ($destacados as $item): ?>
          <div class="featured-item">
            <img src="thumbnail.php?id_contenido=<?= $item['id_contenido'] ?>" alt="<?= htmlspecialchars($item['titulo']) ?>">
            <div class="featured-content">
              <h3><?= htmlspecialchars($item['titulo']) ?></h3>
              <p><?= nl2br(htmlspecialchars(truncar_texto($item['descripcion'], 150))) ?></p>
              <a href="catalog.php#<?= htmlspecialchars($item['tipo']) ?>" class="btn-primary">Ver más</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
require 'footer.php';

// Función para truncar texto (si no la tienes ya en index.php)
function truncar_texto(string $texto, int $max_chars = 100): string {
    if (mb_strlen($texto) <= $max_chars) {
        return $texto;
    }
    return mb_substr($texto, 0, $max_chars) . '…';
}
?>
