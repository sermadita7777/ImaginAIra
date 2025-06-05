/**
 * IA-Lovers - Upload Script
 * Este archivo contiene la funcionalidad específica para la página de subida de contenido
 */

// Variable global para el usuario actual
let user = localStorage.getItem('currentUser') || 'Usuario';

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar la funcionalidad del menú hamburguesa para dispositivos móviles
    initMobileMenu();

    // Comprobar si hay un usuario logueado


    // Inicializar funcionalidades específicas de la página de subida
    initUploadFunctionality();
});

/**
 * Inicializa el menú hamburguesa para dispositivos móviles
 */
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const navLinksItems = document.querySelectorAll('.nav-links a');

    if (hamburger) {
        // Toggle menú al hacer clic en hamburguesa
        hamburger.addEventListener('click', function () {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        // Cerrar menú al hacer clic en un enlace
        navLinksItems.forEach(item => {
            item.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('active');
                    hamburger.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        });

        // Cerrar menú al hacer clic fuera del menú
        document.addEventListener('click', function (event) {
            const isClickInsideMenu = navLinks.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);

            if (!isClickInsideMenu && !isClickOnHamburger && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });

        // Ajustar menú en cambio de tamaño de ventana
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
}

/**
 * Comprueba si hay un usuario logueado y actualiza la interfaz
 */
function checkUserLogin() {
    const currentUserData = localStorage.getItem('currentUser');
    const loginBtn = document.querySelector('.login-btn');
    const registerBtn = document.querySelector('.register-btn');
    const userIcon = document.querySelector('.nav-links a[href="profile.html"]');
    const uploadForm = document.getElementById('uploadForm');
    const uploadNotice = document.createElement('div');

    // Verificar si hay un usuario logueado
    if (currentUserData) {
        try {
            // Parsear los datos del usuario
            const currentUser = JSON.parse(currentUserData);

            // Actualizar la interfaz para usuario logueado
            if (loginBtn) loginBtn.style.display = 'none';
            if (registerBtn) registerBtn.style.display = 'none';
            if (userIcon) {
                userIcon.innerHTML = `<i class="fas fa-user"></i> ${currentUser.username || 'Usuario'}`;
                userIcon.style.display = 'block';
            }

            // Actualizar la variable global del usuario
            user = currentUser.username || 'Usuario';

            // Mostrar mensaje de bienvenida en la página
            const welcomeMessage = document.querySelector('.section-header p');
            if (welcomeMessage) {
                welcomeMessage.textContent = `¡Hola ${currentUser.username || 'Usuario'}! Comparte tus creaciones generadas con IA con la comunidad`;
            }
        } catch (error) {
            console.error('Error al parsear datos de usuario:', error);
            localStorage.removeItem('currentUser'); // Limpiar datos corruptos
        }
    } else {
        // Usuario no logueado
        if (userIcon) userIcon.innerHTML = `<i class="fas fa-user"></i>`;

        // Mostrar aviso de inicio de sesión en el formulario
        if (uploadForm) {
            uploadNotice.className = 'login-notice';
            uploadNotice.innerHTML = `
                <div style="background-color: rgba(79, 70, 229, 0.1); border-left: 4px solid var(--primary-color); padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="margin: 0; color: var(--text-color);"><i class="fas fa-info-circle"></i> Para subir contenido, necesitas <a href="login.html" style="color: var(--primary-color); font-weight: 600;">iniciar sesión</a> o <a href="register.html" style="color: var(--primary-color); font-weight: 600;">registrarte</a>.</p>
                </div>
            `;
            uploadForm.insertBefore(uploadNotice, uploadForm.firstChild);
        }
    }
}

/**
 * Inicializa la funcionalidad específica de la página de subida
 */
function initUploadFunctionality() {
    // Inicializar la previsualización de archivos
    initFilePreview();

    // Inicializar la selección de etiquetas
    initTagSelection();

    // Inicializar el formulario de subida
    initUploadForm();
}

/**
 * Inicializa la previsualización de archivos
 */
function initFilePreview() {
    const fileInput = document.getElementById('contentFile');
    const filePreview = document.getElementById('filePreview');
    const previewImg = document.getElementById('previewImg');
    const fileUploadContainer = document.querySelector('.file-upload-container');
    const uploadIcon = document.querySelector('.file-upload-container i');
    const uploadText = document.querySelector('.file-upload-container p');
    const contentTypeSelect = document.getElementById('contentType');

    if (fileInput && filePreview && previewImg) {
        // Función para validar el tipo de archivo según el tipo de contenido seleccionado
        function validateFileType(file) {
            if (!file) return false;

            const contentType = contentTypeSelect ? contentTypeSelect.value : '';
            const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            const validLiteratureTypes = ['application/pdf', 'text/plain'];

            if ((contentType === 'memes' || contentType === 'arte') && !validImageTypes.includes(file.type)) {
                alert('Para memes y arte, solo se permiten archivos en formato JPG o PNG.');
                return false;
            }

            if (contentType === 'literatura' && !validLiteratureTypes.includes(file.type)) {
                alert('Para literatura, solo se permiten archivos en formato PDF o TXT.');
                return false;
            }

            return true;
        }

        // Función para manejar la selección de archivos
        function handleFileSelect(file) {
            if (!file) return;

            // Validar el tipo de archivo si se ha seleccionado un tipo de contenido
            if (contentTypeSelect && contentTypeSelect.value && !validateFileType(file)) {
                fileInput.value = ''; // Limpiar el input de archivo
                return;
            }

            // Comprobar si es una imagen
            if (file.type.match('image.*')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    filePreview.style.display = 'block';
                    fileUploadContainer.style.display = 'none';
                };

                reader.readAsDataURL(file);
            } else {
                // Si no es una imagen, mostrar un icono según el tipo de archivo
                let fileIcon = '';
                let iconColor = '#95a5a6'; // Color por defecto

                if (file.type.includes('pdf') || file.name.endsWith('.pdf')) {
                    fileIcon = 'fa-file-pdf';
                    iconColor = '#e74c3c'; // Rojo para PDF
                } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                    fileIcon = 'fa-file-word';
                    iconColor = '#2980b9'; // Azul para Word
                } else if (file.type.includes('text') || file.name.endsWith('.txt')) {
                    fileIcon = 'fa-file-alt';
                    iconColor = '#7f8c8d'; // Gris para texto
                } else {
                    fileIcon = 'fa-file';
                }

                filePreview.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                        <i class="fas ${fileIcon}" style="font-size: 4rem; color: ${iconColor};"></i>
                        <p style="margin-top: 10px; font-weight: 500;">${file.name}</p>
                        <p style="font-size: 0.8rem; color: #95a5a6;">${formatFileSize(file.size)}</p>
                    </div>
                `;

                filePreview.style.display = 'block';
                fileUploadContainer.style.display = 'none';
            }
        }

        // Evento change para el input de archivo
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            handleFileSelect(file);
        });

        // Permitir arrastrar y soltar archivos
        fileUploadContainer.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = 'var(--primary-color)';
            this.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
        });

        fileUploadContainer.addEventListener('dragleave', function () {
            this.style.borderColor = 'rgba(0, 0, 0, 0.1)';
            this.style.backgroundColor = 'transparent';
        });

        fileUploadContainer.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = 'rgba(0, 0, 0, 0.1)';
            this.style.backgroundColor = 'transparent';

            if (e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];

                // Validar el tipo de archivo antes de procesarlo
                if (contentTypeSelect && contentTypeSelect.value && !validateFileType(file)) {
                    return; // No procesar el archivo si no es válido
                }

                fileInput.files = e.dataTransfer.files;
                handleFileSelect(file);
            }
        });

        // Botón para cambiar el archivo seleccionado
        const resetFileButton = document.createElement('button');
        resetFileButton.type = 'button';
        resetFileButton.className = 'btn-secondary';
        resetFileButton.style.position = 'absolute';
        resetFileButton.style.top = '10px';
        resetFileButton.style.right = '10px';
        resetFileButton.style.padding = '5px 10px';
        resetFileButton.style.fontSize = '0.8rem';
        resetFileButton.innerHTML = '<i class="fas fa-redo"></i> Cambiar';
        resetFileButton.addEventListener('click', function () {
            filePreview.style.display = 'none';
            fileUploadContainer.style.display = 'flex';
            fileInput.value = '';
        });

        filePreview.style.position = 'relative';
        filePreview.appendChild(resetFileButton);
    }

    // Inicializar también la miniatura
    const thumbnailInput = document.getElementById('contentThumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const previewThumbnail = document.getElementById('previewThumbnail');
    const thumbnailUploadContainer = document.querySelector('.thumbnail-upload-container');

    if (thumbnailInput && thumbnailPreview && previewThumbnail && thumbnailUploadContainer) {
        // Función para manejar la selección de archivos de miniatura
        function handleThumbnailSelect(file) {
            if (!file) return;

            // Validar que sea una imagen
            if (!file.type.match('image.*')) {
                alert('La miniatura debe ser una imagen (JPG o PNG)');
                thumbnailInput.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                previewThumbnail.src = e.target.result;
                thumbnailPreview.style.display = 'block';
                thumbnailUploadContainer.style.display = 'none';
            };

            reader.readAsDataURL(file);
        }

        // Evento change para el input de miniatura
        thumbnailInput.addEventListener('change', function () {
            const file = this.files[0];
            handleThumbnailSelect(file);
        });

        // Permitir arrastrar y soltar archivos para la miniatura
        thumbnailUploadContainer.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = 'var(--primary-color)';
            this.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
        });

        thumbnailUploadContainer.addEventListener('dragleave', function () {
            this.style.borderColor = 'rgba(0, 0, 0, 0.1)';
            this.style.backgroundColor = 'rgba(0, 0, 0, 0.02)';
        });

        thumbnailUploadContainer.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = 'rgba(0, 0, 0, 0.1)';
            this.style.backgroundColor = 'rgba(0, 0, 0, 0.02)';

            if (e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];
                thumbnailInput.files = e.dataTransfer.files;
                handleThumbnailSelect(file);
            }
        });

        // Botón para cambiar la miniatura seleccionada
        const resetThumbnailButton = document.createElement('button');
        resetThumbnailButton.type = 'button';
        resetThumbnailButton.className = 'btn-secondary';
        resetThumbnailButton.style.position = 'absolute';
        resetThumbnailButton.style.top = '10px';
        resetThumbnailButton.style.right = '10px';
        resetThumbnailButton.style.padding = '5px 10px';
        resetThumbnailButton.style.fontSize = '0.8rem';
        resetThumbnailButton.innerHTML = '<i class="fas fa-redo"></i> Cambiar';
        resetThumbnailButton.addEventListener('click', function () {
            thumbnailPreview.style.display = 'none';
            thumbnailUploadContainer.style.display = 'flex';
            thumbnailInput.value = '';
        });

        thumbnailPreview.style.position = 'relative';
        thumbnailPreview.appendChild(resetThumbnailButton);
    }
}

/**
 * Formatea el tamaño del archivo a una unidad legible
 * @param {number} bytes - Tamaño en bytes
 * @returns {string} - Tamaño formateado
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function initTagSelection() {
    const tags = document.querySelectorAll('.tag');
    const contentTypeSelect = document.getElementById('contentType');
    const literatureTags = document.getElementById('literatureTags');
    const fileInput = document.getElementById('contentFile');
    const fileFormatInfo = document.querySelector('.file-upload-container small');
    const thumbnailGroup = document.querySelector('.thumbnail-group');
    const thumbnailInput = document.getElementById('contentThumbnail');

    if (tags && contentTypeSelect && literatureTags) {
        contentTypeSelect.addEventListener('change', function () {
            // Gestionar la visualización de etiquetas de literatura y miniatura
            if (this.value === 'literatura') {
                literatureTags.style.display = 'block';
                thumbnailGroup.style.display = 'block';
                thumbnailInput.required = false; // Cambiado a false para hacerlo opcional

                if (fileInput) {
                    fileInput.setAttribute('accept', 'application/pdf,text/plain');
                    if (fileFormatInfo) {
                        fileFormatInfo.textContent = 'Formato permitido: PDF para literatura';
                    }
                }
            } else {
                literatureTags.style.display = 'none';
                thumbnailGroup.style.display = 'none';
                thumbnailInput.required = false;
                thumbnailInput.value = ''; // Limpiar la miniatura si se cambia el tipo

                if (fileInput) {
                    fileInput.setAttribute('accept', 'image/jpeg,image/jpg,image/png');
                    if (fileFormatInfo) {
                        fileFormatInfo.textContent = 'Formatos permitidos: JPG/PNG para memes y arte';
                    }
                }
            }
        });

        // Inicializar estado según el valor actual
        contentTypeSelect.dispatchEvent(new Event('change'));
    }
}

/**
 * Inicializa el formulario de subida
 */
function initUploadForm() {
    const uploadForm = document.getElementById('uploadForm');
    const uploadContainer = document.querySelector('.upload-container');
    const uploadSuccess = document.querySelector('.upload-success');

    if (uploadForm && uploadContainer && uploadSuccess) {
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Obtener los valores del formulario
            const title = document.getElementById('contentTitle').value;
            const description = document.getElementById('contentDescription').value;
            const type = document.getElementById('contentType').value;
            const file = document.getElementById('contentFile').files[0];
            const thumbnail = document.getElementById('contentThumbnail').files[0];

            // Obtener las etiquetas seleccionadas
            const selectedTags = [];
            document.querySelectorAll('.tag.selected').forEach(tag => {
                selectedTags.push(tag.getAttribute('data-tag'));
            });

            // Validar que se hayan completado los campos obligatorios
            if (!title || !description || !type || !file) {
                alert('Por favor, completa todos los campos obligatorios.');
                return;
            }

            // Validar el formato de archivo según el tipo de contenido
            const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
			const validLiteratureTypes = ['application/pdf', 'text/plain'];


            if ((type === 'memes' || type === 'arte') && !validImageTypes.includes(file.type)) {
                alert('Para memes y arte, solo se permiten archivos en formato JPG o PNG.');
                return;
            }

            if (type === 'literatura' && !validLiteratureTypes.includes(file.type)) {
                alert('Para literatura, solo se permiten archivos en formato PDF o TXT.');
                return;
            }

            // Obtener información del usuario actual
            const currentUser = JSON.parse(localStorage.getItem('currentUser'));
            if (!currentUser) {
                alert('Debes iniciar sesión para publicar contenido.');
                window.location.href = 'login.html';
                return;
            }

            // Crear un objeto con los datos del contenido
            const contentData = {
                id: 'content_' + Date.now(),
                title: title,
                description: description,
                type: type,
                fileName: file.name,
                fileSize: file.size,
                fileType: file.type,
                thumbnailName: thumbnail ? thumbnail.name : null,
                tags: selectedTags,
                author: currentUser.username || user,
                authorId: currentUser.id || 'anonymous',
                date: new Date().toISOString(),
                likes: 0,
                downloads: 0
            };

            // En un caso real, aquí se enviarían los datos al servidor
            // Para esta demo, guardaremos los datos en localStorage

            // Obtener los contenidos existentes
            const userContent = JSON.parse(localStorage.getItem('userContent')) || [];

            // Agregar el nuevo contenido
            userContent.push(contentData);

            // Guardar en localStorage
            localStorage.setItem('userContent', JSON.stringify(userContent));

            // Actualizar contador de contenido del usuario
            updateUserContentCount(currentUser);

            // Mostrar mensaje de éxito
            uploadContainer.style.display = 'none';
            uploadSuccess.style.display = 'block';

            // Redireccionar al catálogo después de 3 segundos
            setTimeout(function () {
                window.location.href = 'catalog.html';
            }, 3000);
        });
    }
}

/**
 * Actualiza el contador de contenido del usuario
 * @param {Object} user - El objeto de usuario actual
 */
function updateUserContentCount(user) {
    if (user && user.id) {
        // Obtener contenido del usuario
        const userContent = JSON.parse(localStorage.getItem('userContent')) || [];
        const userItems = userContent.filter(item => item.authorId === user.id);

        // Actualizar contador en el objeto de usuario
        user.contentCount = userItems.length;
        localStorage.setItem('currentUser', JSON.stringify(user));

        // También actualizar en el array de usuarios si existe
        const users = JSON.parse(localStorage.getItem('users')) || [];
        const userIndex = users.findIndex(u => u.id === user.id);

        if (userIndex !== -1) {
            users[userIndex].contentCount = userItems.length;
            localStorage.setItem('users', JSON.stringify(users));
        }
    }
}
