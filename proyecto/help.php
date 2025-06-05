<?php
// help.php
require 'header.php'; // Incluye conexión, sesiones, cabecera HTML, etc.
?>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }
        .helpSection {
            padding: 40px 20px;
            max-width: 800px;
            margin: auto;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-header h2 {
            font-size: 2em;
            color: #333;
            margin-bottom: 10px;
        }
        .underline {
            width: 50px;
            height: 4px;
            background-color: #007bff;
            margin-bottom: 20px;
        }
        .section-description,
        .section-info {
            margin-bottom: 30px;
        }
        .section-info h2 {
            font-size: 1.4em;
            margin-bottom: 10px;
            color: #222;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>

    <section class="helpSection">
        <div class="container">
            <div class="section-header">
                <h2>Guía IA-Lovers</h2>
                <div class="underline"></div>
            </div>

            <div class="section-description">
                <p>En esta guía aprenderás a usar <i>IA-Lovers</i>, una plataforma donde puedes compartir tus memes, arte o literatura con una comunidad apasionada por la creatividad y la inteligencia artificial.</p>
            </div>

            <div class="section-info">
                <h2>¿Cómo publicar contenido?</h2>
                <p>Para publicar contenido es muy sencillo: dirígete a la sección de <a href="profile.php">"Mi perfil"</a>, y allí encontrarás un botón que dice <strong>"Publica algo"</strong>. Desde allí podrás subir tu archivo, ponerle un título, una foto, una descripción, y elegir la categoría (meme, arte o literatura).</p>
            </div>

            <div class="section-info">
                <h2>¿Dónde puedo ver mi actividad?</h2>
                <p>Para poder ver el resumen de tu actividad (publicaciones que hayas dado "me gusta", en carrito, o tu información de usuario, deberás dirigirte a <a href="profile.php">"Mi perfil"</a>, y allí tendrás todo.</p>
            </div>
            <div class="section-info">
                <h2>¿Cómo ver el contenido de otros usuarios?</h2>
                <p>Ve al <a href="catalog.php">catálogo</a>, donde podrás explorar todo el contenido publicado por la comunidad. Puedes usar filtros para ver solo memes, arte o literatura.</p>
            </div>

            <div class="section-info">
                <h2>¿Cómo dar "Me gusta" a una publicación?</h2>
                <p>En cada publicación del catálogo, verás un ícono de corazón ❤️. Haz clic para dar "Me gusta". Si ya diste "Me gusta", el ícono aparecerá resaltado y se guardará la publicación en tu sección de 
                   <a href="likes.php">"favoritos"</a> </p>
            </div>

            <div class="section-info">
                <h2>¿Cómo agregar contenido al carrito?</h2>
                <p>Puedes agregar contenido al carrito haciendo clic en el ícono 🛒 disponible en cada publicación. Esto te permite guardar en <a href="cart.php">"mi carrito"</a>, contenido que te interesa para revisarlo más tarde o descargarlo.</p>
            </div>

            <div class="section-info">
                <h2>¿Dónde ver mi contenido publicado?</h2>
                <p>En <a href="profile.php">"Mi perfil"</a> podrás ver todas tus publicaciones, la fecha en la que las subiste, y gestionarlas (eliminarlas o editarlas).</p>
            </div>

            <div class="section-info">
                <h2>¿Cómo cambiar mi información de usuario?</h2>
                <p>Desde tu perfil puedes acceder a la configuración de cuenta (próximamente), donde podrás cambiar tu nombre de usuario, correo electrónico o contraseña.</p>
            </div>

            <div class="section-info">
                <h2>¿Quién puede ver mis publicaciones?</h2>
                <p>Todo el contenido publicado en IA-Lovers es público y visible para cualquier usuario registrado. Publica solo lo que quieras compartir con los demás.</p>
            </div>

            <div class="section-info">
                <h2>¿Dónde pedir ayuda o reportar problemas?</h2>
                <p>Si necesitas ayuda o quieres reportar un error, puedes escribirnos a <a href="mailto:soporte@ialovers.com">soporte@ialovers.com</a> o utilizar el formulario de contacto en el pie de página.</p>
            </div>
        </div>
    </section>

<?php require 'footer.php'; ?>
