<?php
// likes.php
require 'header.php';
if (!$user) { header('Location: login.php'); exit; }
?>
<link rel="stylesheet" href="css/likes.css">
<main class="section">
  <div class="container">
    <h2>Publicaciones que me gustaron</h2>
    <div id="likesGrid" class="grid"></div>
  </div>
</main>
<script defer src="js/likes.js"></script>
<?php require 'footer.php'; ?>
