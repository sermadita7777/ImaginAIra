<?php
// index.php
require 'header.php';
?>
<link rel="stylesheet" href="css/index.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <h1>Bienvenido a IA-Lovers</h1>
      <p class="slogan">Tu plataforma para descubrir contenido generado por IA</p>
      <p class="subtitle">
        Explora nuestra colección de memes, arte y literatura creados con inteligencia artificial
      </p>
      <div class="hero-buttons">
        <a href="catalog.php" class="btn-primary">
          Explorar Catálogo <i class="fas fa-arrow-right"></i>
        </a>
        <a href="login.php" class="btn-secondary">Iniciar Sesión</a>
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
      <div class="category-card">
        <div class="category-icon"><i class="fas fa-image"></i></div>
        <h3>Memes</h3>
        <p>Descubre divertidos memes generados por inteligencia artificial</p>
        <a href="catalog.php#memes" class="category-link">
          Ver Galería <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <div class="category-card">
        <div class="category-icon"><i class="fas fa-paint-brush"></i></div>
        <h3>Arte</h3>
        <p>Explora increíbles obras de arte creadas con ayuda de la IA</p>
        <a href="catalog.php#arte" class="category-link">
          Ver Galería <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <div class="category-card">
        <div class="category-icon"><i class="fas fa-book"></i></div>
        <h3>Literatura</h3>
        <p>Sumérgete en historias fascinantes generadas por IA</p>
        <a href="catalog.php#literatura" class="category-link">
          Ver Biblioteca <i class="fas fa-arrow-right"></i>
        </a>
      </div>
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
      <div class="featured-item">
        <img src="imagenes/featured-1.svg" alt="Imagen destacada 1">
        <div class="featured-content">
          <h3>Ejemplo1</h3>
          <p>Una impresionante vista generada por IA que combina elementos surrealistas</p>
          <a href="catalog.php#arte" class="btn-primary">Ver más</a>
        </div>
      </div>
      <div class="featured-item">
        <img src="imagenes/featured-2.svg" alt="Imagen destacada 2">
        <div class="featured-content">
          <h3>El Viajero del Tiempo</h3>
          <p>Relato corto sobre un viajero que descubre los secretos del universo</p>
          <a href="catalog.php#literatura" class="btn-primary">Ver más</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require 'footer.php';
