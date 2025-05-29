<?php
// catalog.php
require 'header.php';
?>
<link rel="stylesheet" href="css/catalog.css">
<section class="section">
  <div class="container">
    <input type="text" id="searchInput" placeholder="Buscar contenido..." class="search-bar">
  </div>
</section>
<section class="section section-tabs">
  <div class="container">
    <button class="tab-btn active" data-cat="memes">Memes</button>
    <button class="tab-btn" data-cat="arte">Arte</button>
    <button class="tab-btn" data-cat="literatura">Literatura</button>
  </div>
</section>
<section id="memes" class="catalog active"><div class="container"><div id="memesGrid" class="grid"></div></div></section>
<section id="arte" class="catalog"><div class="container"><div id="arteGrid" class="grid"></div></div></section>
<section id="literatura" class="catalog">
  <div class="container">
    <div class="lit-filters">
      <label><input type="checkbox" class="lit-filter" value="Fantasía" checked> Fantasía</label>
      <label><input type="checkbox" class="lit-filter" value="Novela" checked> Novela</label>
      <label><input type="checkbox" class="lit-filter" value="Misterio" checked> Misterio</label>
    </div>
    <div id="literaturaGrid" class="grid"></div>
  </div>
</section>
<script defer src="js/script.js"></script>
<?php require 'footer.php'; ?>
