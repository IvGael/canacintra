// Función para inicializar el escáner de QR automáticamente
function inicializarEscanerQR() {
    const html5QrCode = new Html5Qrcode("reader"); // Inicializa el escáner en el contenedor #reader

    // Configuración del escáner
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    // Iniciar el escaneo automáticamente con la cámara trasera
    html5QrCode.start(
        { facingMode: "environment" }, // Usar la cámara trasera
        config,
        (decodedText, decodedResult) => {
            // Acción cuando se escanea el código QR correctamente
            console.log(`Código QR escaneado: ${decodedText}`);
            document.getElementById('qrResult').innerHTML = `Código QR escaneado: ${decodedText}`;
            // Aquí puedes hacer lo que necesites con el código escaneado (por ejemplo, enviarlo al servidor)
        },
        (errorMessage) => {
            // Manejar los errores de escaneo
            console.warn(`Error en el escaneo: ${errorMessage}`);
        }
    ).catch((err) => {
        // Manejo de errores si no se puede acceder a la cámara
        console.error("Error al acceder a la cámara: ", err);
        alert("No se pudo acceder a la cámara. Asegúrate de que tu navegador tiene permisos.");
    });
}

// Función para cargar usuarios desde la base de datos
function cargarUsuarios() {
    $.ajax({
        url: 'obtener_usuarios.php', // Ruta al archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const tablaUsuarios = document.getElementById('usuariosRegistrados');
            tablaUsuarios.innerHTML = ''; // Limpiar el contenido de la tabla

            response.forEach((usuario) => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${usuario.nombre}</td>
                    <td>${usuario.apellido}</td>
                    <td>${usuario.correo_electronico}</td>
                    <td>${usuario.telefono ? usuario.telefono : 'N/A'}</td>
                    <td>${usuario.empresa ? usuario.empresa : 'N/A'}</td>
                    <td>${usuario.puesto ? usuario.puesto : 'N/A'}</td>
                    <td>
                        <button class="btn btn-warning" onclick="editarUsuario(${usuario.id})">Editar</button>
                        <button class="btn btn-danger" onclick="eliminarUsuario(${usuario.id})">Eliminar</button>
                    </td>
                `;
                tablaUsuarios.appendChild(fila);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar usuarios: ', error);
        }
    });
}

// Función para filtrar usuarios
function filtrarUsuarios() {
    const input = document.getElementById('buscarUsuario');
    const filter = input.value.toLowerCase();
    const tablaUsuarios = document.getElementById('usuariosRegistrados');
    const filas = tablaUsuarios.getElementsByTagName('tr');

    for (let i = 0; i < filas.length; i++) {
        const celdas = filas[i].getElementsByTagName('td');
        let coincide = false;

        for (let j = 0; j < celdas.length - 1; j++) { // Evita la columna de acciones
            if (celdas[j] && celdas[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                coincide = true;
                break;
            }
        }

        filas[i].style.display = coincide ? '' : 'none';
    }
}

// Evento para el campo de búsqueda
document.getElementById('buscarUsuario').addEventListener('input', filtrarUsuarios);

// Inicializar todo al cargar la página
$(document).ready(function() {
    inicializarEscanerQR(); // Iniciar el escáner QR automáticamente
    cargarUsuarios(); // Cargar los usuarios
});
