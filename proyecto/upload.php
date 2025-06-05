<?php
// upload.php
require 'header.php';

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  define('MAX_FILE_SIZE', 1 * 1024 * 1024);
  $titulo = trim($_POST['contentTitle']);
  $tipo   = $_POST['contentType'];
  $desc   = trim($_POST['contentDescription']);
  $file   = $_FILES['contentFile'];
  $miniatura_blob = null;
  $miniatura_mime = null;

  // Validaciones básicas
  if (!$titulo) $errors[] = 'El título es obligatorio.';
  if (!in_array($tipo, ['memes', 'arte', 'literatura']))
    $errors[] = 'Tipo de contenido no válido.';
  if ($file['error'] !== UPLOAD_ERR_OK)
    $errors[] = 'Error al subir el archivo.';
  if ($file['size'] > MAX_FILE_SIZE)
    $errors[] = 'Archivo demasiado grande (máx 1 MB).';

  $allowed = $tipo === 'literatura'
    ? ['application/pdf']
    : ['image/jpeg', 'image/png'];
  if (!in_array($file['type'], $allowed))
    $errors[] = 'Formato no permitido para este tipo.';

  // Validar miniatura (opcional)
  if (isset($_FILES['contentThumbnail']) && $_FILES['contentThumbnail']['error'] === UPLOAD_ERR_OK) {
    $miniatura = $_FILES['contentThumbnail'];
    $allowedThumb = ['image/jpeg', 'image/png'];
    if (in_array($miniatura['type'], $allowedThumb) && $miniatura['size'] <= MAX_FILE_SIZE) {
      $miniatura_blob = file_get_contents($miniatura['tmp_name']);
      $miniatura_mime = $miniatura['type'];
    } else {
      $errors[] = 'Miniatura inválida o demasiado grande.';
    }
  }

  // Capturar etiquetas (solo literatura)
  $etiquetas = [];
  if ($tipo === 'literatura' && !empty($_POST['tags'])) {
    $etiquetas = array_filter(array_map('trim', explode(',', $_POST['tags'])));
  }

  if (empty($errors)) {
    // Insertar contenido en la BD
    $stmt = $pdo->prepare("
      INSERT INTO contenidos
        (usuario_id, tipo, titulo, descripcion, datos_blob, tipo_mime, tamano_bytes, miniatura_blob, miniatura_mime)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
      $_SESSION['user_id'],
      $tipo,
      $titulo,
      $desc,
      file_get_contents($file['tmp_name']),
      $file['type'],
      $file['size'],
      $miniatura_blob,
      $miniatura_mime
    ]);
    $contenido_id = $pdo->lastInsertId();

    // Insertar etiquetas si es literatura
    if ($tipo === 'literatura' && !empty($etiquetas)) {
        $stmtGetTagId = $pdo->prepare("SELECT id FROM etiquetas WHERE nombre = ?");
        $stmtTag = $pdo->prepare("INSERT INTO contenido_etiquetas (contenido_id, etiqueta_id) VALUES (?, ?)");

        foreach ($etiquetas as $tagName) {
          $stmtGetTagId->execute([$tagName]);
          $tagId = $stmtGetTagId->fetchColumn();

          if ($tagId) {
            $stmtTag->execute([$contenido_id, $tagId]);
          }
        }
    }

    $success = true;
  }
}
?>

<link rel="stylesheet" href="css/upload.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="js/upload.js"></script>
<style>
  .tag.active {
    background-color: #4caf50;
    color: white;
    cursor: pointer;
  }
  .error-message {
    color: #d93025;
    margin-bottom: 10px;
  }
  .success-message {
    color: #188038;
    margin-bottom: 10px;
  }
</style>

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>Subir Contenido</h2>
      <div class="underline"></div>
      <br>
      <p>Comparte tus creaciones con la comunidad</p>
    </div>

    <?php if ($errors): ?>
      <div class="error-message">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?=htmlspecialchars($error)?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($success): ?>
      <div class="success-message">
        ¡Contenido subido correctamente!
      </div>
    <?php endif; ?>

    <div class="upload-container">
      <form id="uploadForm" class="upload-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="tags" id="tagsInput" value="">

        <div class="form-group">
          <label for="contentTitle">Título *</label>
          <input type="text" id="contentTitle" name="contentTitle" class="form-control" placeholder="Título de tu contenido" required>
        </div>

        <div class="form-group">
          <label for="contentType">Tipo de Contenido *</label>
          <select id="contentType" name="contentType" class="form-control" required>
            <option value="" disabled selected>Selecciona un tipo</option>
            <option value="memes">Meme</option>
            <option value="arte">Arte</option>
            <option value="literatura">Literatura</option>
          </select>
        </div>

        <div class="form-group full-width">
          <label for="contentDescription">Descripción *</label>
          <textarea id="contentDescription" name="contentDescription" class="form-control" placeholder="Describe tu contenido..." required></textarea>
        </div>

        <div class="form-group full-width">
          <label>Archivo Principal *</label>
          <div class="file-upload-container">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Arrastra y suelta tu archivo aquí o haz clic para seleccionar</p>
            <input type="file" id="contentFile" name="contentFile" accept="image/jpeg,image/jpg,image/png,application/pdf" required>
            <small>Formatos permitidos: JPG/PNG para memes y arte, PDF para literatura</small>
          </div>
          <div id="filePreview" class="file-preview">
            <img id="previewImg" src="#" alt="Vista previa">
          </div>
        </div>

        <div class="form-group thumbnail-group">
          <label>Miniatura (opcional)</label>
          <div class="thumbnail-upload-container">
            <i class="fas fa-image"></i>
            <p>Arrastra una imagen o haz clic para seleccionar</p>
            <small>Solo JPG/PNG - Recomendado: 300x200px (opcional)</small>
            <input type="file" id="contentThumbnail" name="contentThumbnail" accept="image/jpeg,image/jpg,image/png">
          </div>
          <div id="thumbnailPreview" class="thumbnail-preview">
            <img id="previewThumbnail" src="#" alt="Vista previa de miniatura">
          </div>
        </div>

        <div id="literatureTags" class="form-group" style="display: none;">
          <label>Etiquetas para Literatura</label>
          <div class="tags-container">
            <div class="tag" data-tag="poesia"><i class="fas fa-feather-alt"></i> Poesía</div>
            <div class="tag" data-tag="ensayo"><i class="fas fa-pen-fancy"></i> Ensayo</div>
            <div class="tag" data-tag="ciencia-ficcion"><i class="fas fa-rocket"></i> Ciencia Ficción</div>
            <div class="tag" data-tag="fantasia"><i class="fas fa-hat-wizard"></i> Fantasía</div>
            <div class="tag" data-tag="terror"><i class="fas fa-ghost"></i> Terror</div>
            <div class="tag" data-tag="romance"><i class="fas fa-heart"></i> Romance</div>
            <div class="tag" data-tag="misterio"><i class="fas fa-search"></i> Misterio</div>
          </div>
        </div>

        <div class="upload-actions">
          <button type="button" class="btn-secondary" onclick="window.location.href='profile.php'">Cancelar</button>
          <button type="submit" class="btn-primary">Publicar Contenido</button>
        </div>
      </form>
    </div>
  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tagsContainer = document.querySelector('.tags-container');
  const tagsInput = document.getElementById('tagsInput');
  let selectedTags = new Set();

  if (!tagsContainer) return;

  tagsContainer.querySelectorAll('.tag').forEach(tag => {
    tag.addEventListener('click', () => {
      const tagName = tag.getAttribute('data-tag');
      if (selectedTags.has(tagName)) {
        selectedTags.delete(tagName);
        tag.classList.remove('active');
      } else {
        selectedTags.add(tagName);
        tag.classList.add('active');
      }
      tagsInput.value = Array.from(selectedTags).join(',');
    });
  });

  // Mostrar etiquetas solo si es literatura
  const contentTypeSelect = document.getElementById('contentType');
  const literatureTagsDiv = document.getElementById('literatureTags');

  contentTypeSelect.addEventListener('change', () => {
    if (contentTypeSelect.value === 'literatura') {
      literatureTagsDiv.style.display = 'block';
    } else {
      literatureTagsDiv.style.display = 'none';
      selectedTags.clear();
      tagsInput.value = '';
      literatureTagsDiv.querySelectorAll('.tag').forEach(tag => tag.classList.remove('active'));
    }
  });
});
</script>

<?php require 'footer.php'; ?>
