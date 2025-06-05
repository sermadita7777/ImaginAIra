<?php
//admin.php
require 'header.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    echo "<section style='padding: 50px; text-align: center; font-family: Poppins, sans-serif;'>
            <h2 style='color: #c00;'>Acceso denegado</h2>
            <p>Debes iniciar sesión para acceder al panel de administración.</p>
            <a href='login.php' class='btn-primary' style='margin-top: 20px; display: inline-block;'>Iniciar sesión</a>
          </section>";
    require 'footer.php';
    exit;
}

// Verificar si el usuario tiene rol de administrador
$usuario_actual = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$usuario_actual]);
$rol = $stmt->fetchColumn();

if ($rol !== 'admin') {
    echo "<section style='padding: 50px; text-align: center; font-family: Poppins, sans-serif;'>
            <h2 style='color: #c00;'>Acceso restringido</h2>
            <p>Esta sección es solo para administradores.</p>
            <a href='index.php' class='btn-primary' style='margin-top: 20px; display: inline-block;'>Volver al inicio</a>
          </section>";
    require 'footer.php';
    exit;
} 


// Obtener usuarios
$usuarios = $pdo->query("SELECT id_usuarios, nombre_usuario, email, created_at, rol FROM usuarios")->fetchAll();

// Obtener contenidos por tipo
$memes = $pdo->query("SELECT id_contenido, titulo, descripcion, created_at, usuario_id FROM contenidos WHERE tipo = 'memes'")->fetchAll();
$arte = $pdo->query("SELECT id_contenido, titulo, descripcion, created_at, usuario_id FROM contenidos WHERE tipo = 'arte'")->fetchAll();
$literatura = $pdo->query("SELECT id_contenido, titulo, descripcion, created_at, usuario_id FROM contenidos WHERE tipo = 'literatura'")->fetchAll();
?>
<link rel="stylesheet" href="css/styles.css"/>
<link rel="stylesheet" href="css/admin.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

<section class="admin-page section">
  <div class="container">
    <div class="admin-header">
      <div class="section-header">
        <h2>Panel de Administración</h2>
        <div class="underline"></div>
      </div>
      <div class="admin-actions">
        <button class="btn-primary" id="upload-content-btn">
          <i class="fas fa-plus"></i> Subir Nuevo Contenido
        </button>
        <button class="btn-secondary" id="refresh-list-btn">
          <i class="fas fa-sync-alt"></i> Actualizar Lista
        </button>
      </div>
    </div>

    <!-- Pestañas de administración -->
    <div class="admin-tabs" style="display: flex; margin-bottom: 30px; border-bottom: 1px solid rgba(0, 0, 0, 0.1);">
      <div class="admin-tab active" data-section="memes-admin" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 500; position: relative;">
        Memes
      </div>
      <div class="admin-tab" data-section="arte-admin" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 500; position: relative;">
        Arte
      </div>
      <div class="admin-tab" data-section="literatura-admin" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 500; position: relative;">
        Literatura
      </div>
      <div class="admin-tab" data-section="usuarios-admin" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 500; position: relative;">
        Usuarios
      </div>
    </div>

    <!-- Memes -->
    <div id="memes-admin" class="admin-section">
      <h3>Gestión de Memes</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Usuario ID</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($memes as $meme): ?>
            <tr>
              <form method="POST" action="update_content.php" style="display: contents;">
                <td>
                  <?= htmlspecialchars($meme['id_contenido']) ?>
                  <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($meme['id_contenido']) ?>">
                </td>
                <td><input type="text" name="titulo" value="<?= htmlspecialchars($meme['titulo']) ?>" required></td>
                <td><input type="text" name="descripcion" value="<?= htmlspecialchars($meme['descripcion']) ?>" required></td>
                <td><?= htmlspecialchars($meme['created_at']) ?></td>
                <td><?= htmlspecialchars($meme['usuario_id']) ?></td>
                <td>
                  <button type="submit" class="action-btn edit-btn" title="Guardar Cambios"><i class="fas fa-edit"></i></button>
              </form>
              <form method="POST" action="delete_content.php" style="display: inline-block;">
                <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($meme['id_contenido']) ?>">
                <button type="submit" class="action-btn delete-btn" title="Eliminar Contenido" onclick="return confirm('¿Seguro que quieres eliminar este contenido?');"><i class="fas fa-trash"></i></button>
              </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Arte -->
    <div id="arte-admin" class="admin-section" style="display: none;">
      <h3>Gestión de Arte</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Usuario ID</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arte as $a): ?>
            <tr>
              <form method="POST" action="update_content.php" style="display: contents;">
                <td>
                  <?= htmlspecialchars($a['id_contenido']) ?>
                  <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($a['id_contenido']) ?>">
                </td>
                <td><input type="text" name="titulo" value="<?= htmlspecialchars($a['titulo']) ?>" required></td>
                <td><input type="text" name="descripcion" value="<?= htmlspecialchars($a['descripcion']) ?>" required></td>
                <td><?= htmlspecialchars($a['created_at']) ?></td>
                <td><?= htmlspecialchars($a['usuario_id']) ?></td>
                <td>
                  <button type="submit" class="action-btn edit-btn" title="Guardar Cambios"><i class="fas fa-edit"></i></button>
              </form>
              <form method="POST" action="delete_content.php" style="display: inline-block;">
                <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($a['id_contenido']) ?>">
                <button type="submit" class="action-btn delete-btn" title="Eliminar Contenido" onclick="return confirm('¿Seguro que quieres eliminar este contenido?');"><i class="fas fa-trash"></i></button>
              </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Literatura -->
    <div id="literatura-admin" class="admin-section" style="display: none;">
      <h3>Gestión de Literatura</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Usuario ID</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($literatura as $lit): ?>
            <tr>
              <form method="POST" action="update_content.php" style="display: contents;">
                <td>
                  <?= htmlspecialchars($lit['id_contenido']) ?>
                  <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($lit['id_contenido']) ?>">
                </td>
                <td><input type="text" name="titulo" value="<?= htmlspecialchars($lit['titulo']) ?>" required></td>
                <td><input type="text" name="descripcion" value="<?= htmlspecialchars($lit['descripcion']) ?>" required></td>
                <td><?= htmlspecialchars($lit['created_at']) ?></td>
                <td><?= htmlspecialchars($lit['usuario_id']) ?></td>
                <td>
                  <button type="submit" class="action-btn edit-btn" title="Guardar Cambios"><i class="fas fa-edit"></i></button>
              </form>
              <form method="POST" action="delete_content.php" style="display: inline-block;">
                <input type="hidden" name="id_contenido" value="<?= htmlspecialchars($lit['id_contenido']) ?>">
                <button type="submit" class="action-btn delete-btn" title="Eliminar Contenido" onclick="return confirm('¿Seguro que quieres eliminar este contenido?');"><i class="fas fa-trash"></i></button>
              </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Usuarios -->
    <div id="usuarios-admin" class="admin-section" style="display: none;">
      <h3>Gestión de Usuarios</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Fecha Registro</th>
            <th>Rol</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <form method="POST" action="update_user.php" style="display: contents;">
                <td>
                  <?= htmlspecialchars($u['id_usuarios']) ?>
                  <input type="hidden" name="id_usuarios" value="<?= htmlspecialchars($u['id_usuarios']) ?>">
                </td>
                <td><input type="text" name="nombre_usuario" value="<?= htmlspecialchars($u['nombre_usuario']) ?>" required></td>
                <td><input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required></td>
                <td><?= htmlspecialchars($u['created_at']) ?></td>
                <td><input type="text" name="rol" value="<?= htmlspecialchars($u['rol']) ?>" required></td>
                <td>
                  <button type="submit" class="action-btn edit-btn" title="Guardar Cambios"><i class="fas fa-edit"></i></button>
              </form>
              <form method="POST" action="delete_user.php" style="display: inline-block;">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($u['id_usuarios']) ?>">
                <button type="submit" class="action-btn delete-btn" title="Eliminar Usuario" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');"><i class="fas fa-trash"></i></button>
              </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const adminTabs = document.querySelectorAll('.admin-tab');
    const adminSections = document.querySelectorAll('.admin-section');

    adminTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Desactivar todos los tabs y ocultar secciones
        adminTabs.forEach(t => t.classList.remove('active'));
        adminSections.forEach(sec => sec.style.display = 'none');

        // Activar tab clicado y mostrar sección correspondiente
        tab.classList.add('active');
        const sectionId = tab.getAttribute('data-section');
        document.getElementById(sectionId).style.display = 'block';
      });
    });

    // Botón actualizar lista
    document.getElementById('refresh-list-btn').addEventListener('click', () => {
      location.reload();
    });

    // Botón subir contenido redirige a upload.php
    document.getElementById('upload-content-btn').addEventListener('click', () => {
      window.location.href = 'upload.php';
    });
  });
</script>

<?php include 'footer.php'; ?>

