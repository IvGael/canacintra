$(document).ready(function() {
    // Inicializa el escáner QR al cargar la página
    inicializarEscanerQR();

    // Maneja el envío del formulario de registro de usuario
    $('#registroUsuario').on('submit', function(event) {
        event.preventDefault();

        $.ajax({
            url: 'admin_panel.php',  // Archivo PHP que procesa el registro
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                alert(response.message);

                // Si el registro fue exitoso, llama a imprimirEtiquetaQR con los datos del QR
                if (response.success) {
                    $('#registroUsuario')[0].reset(); // Restablece el formulario

                    // Llama a la función de impresión de etiqueta QR si se recibió qrData
                    if (response.qrData) {
                        imprimirEtiquetaQR(response.qrData);
                    } else {
                        console.error("No se recibió qrData en la respuesta.");
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", xhr, status, error);
                alert("Error en la solicitud. Inténtalo de nuevo.");
            }
        });
    });

    // Carga los usuarios registrados en la tabla
    cargarUsuariosRegistrados();
});

// Función para inicializar el escáner QR
function inicializarEscanerQR() {
    const html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    let isScanning = true; // Control para evitar múltiples escaneos

    html5QrCode.start(
        { facingMode: "environment" }, // Usa la cámara trasera
        config,
        (decodedText, decodedResult) => {
            if (isScanning) {
                isScanning = false; // Deshabilita el escaneo temporalmente
                console.log(`Código QR escaneado: \n${decodedText}`);
                document.getElementById('qrResult').innerHTML = `Código QR escaneado: \n${decodedText}`;

                // Llama a la función para procesar el código QR escaneado
                imprimirEtiquetaQR(decodedText);

                // Vuelve a habilitar el escaneo después de 4 segundos
                setTimeout(() => { isScanning = true; }, 4000);
            }
        },
        (errorMessage) => {
            console.warn(`Error en el escaneo: ${errorMessage}`);
        }
    ).catch((err) => {
        console.error("Error al acceder a la cámara: ", err);
        alert("No se pudo acceder a la cámara.");
    });
}

// Función para imprimir la etiqueta QR
function imprimirEtiquetaQR(qrCodeData) {
    console.log("Printing QR");
    const { jsPDF } = window.jspdf;
    
    const pageWidth = 101.6;
    const pageHeight = 53.97;
    const QRSize = 40;

    // Obtiene el nombre del código QR
    const fnMatch = qrCodeData.match(/FN:(.*)/);
    const name = fnMatch ? fnMatch[1].trim() : "Unknown";
    const qrData = qrCodeData;
    

    // Genera el código QR
    const qr = new QRious({
        value: qrData,
        size: 150
    });

    // Crea el PDF
    const pdf = new jsPDF("landscape", "mm", [pageHeight, pageWidth]);

    // Agrega el nombre
    pdf.setFontSize(16);
    const textWidth = pdf.getTextWidth(name);
    const xCoordinate = (pageWidth - textWidth) / 2;
    pdf.text(name, xCoordinate, 9); // Centra el nombre en la parte superior
    
    // Agrega el código QR
    const qrDataUrl = qr.toDataURL();
    const xQRCordinate = (pageWidth - QRSize) / 2;
    pdf.addImage(qrDataUrl, "PNG", xQRCordinate, 11, QRSize, QRSize);

    // Crea y muestra el PDF en un iframe para imprimir
    const pdfBlob = pdf.output("blob");
    const pdfUrl = URL.createObjectURL(pdfBlob);
    const iframe = document.createElement("iframe");
    iframe.style.position = "absolute";
    iframe.style.width = "0";
    iframe.style.height = "0";
    iframe.style.border = "none";
    document.body.appendChild(iframe);
    iframe.src = pdfUrl;

    iframe.onload = function () {
        iframe.contentWindow.print();
        iframe.contentWindow.onafterprint = function () {
            document.body.removeChild(iframe); // Limpia el iframe
            URL.revokeObjectURL(pdfUrl); // Libera la URL del objeto
        };
    };
}

// Función para cargar los usuarios registrados en la tabla
function cargarUsuariosRegistrados() {
    $.ajax({
        url: 'get_usuarios.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let rows = '';
            data.forEach(function(usuario) {
                rows += `<tr>
                            <td>${usuario.nombre}</td>
                            <td>${usuario.apellido}</td>
                            <td>${usuario.correo_electronico}</td>
                            <td>${usuario.telefono}</td>
                            <td>${usuario.empresa}</td>
                            <td>${usuario.puesto}</td>
                            <td><button class="btn eliminar-usuario" data-id="${usuario.id}">Imprimir</button></td> 
                        </tr>`;
            });
            $('#usuariosRegistrados').html(rows);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar los usuarios:', error);
            alert('Error al cargar los usuarios. Verifica la consola para detalles.'); 
        }
    });

    // Event listener for the "Imprimir" button
    $('#usuariosRegistrados').on('click', '.eliminar-usuario', function() {
        const row = $(this).closest('tr');
        const usuarioData = {
            nombre: row.find('td').eq(0).text(),
            apellido: row.find('td').eq(1).text(),
            correo_electronico: row.find('td').eq(2).text(),
            telefono: row.find('td').eq(3).text(),
            empresa: row.find('td').eq(4).text(),
            puesto: row.find('td').eq(5).text()
        };

        // Call the GenerateQR function and pass usuarioData to it
        GenerateQR(usuarioData);
    });
}

function GenerateQR(usuarioData) {
    // Format the data as a vCard string
    const vCard = `
BEGIN:VCARD
VERSION:3.0
N:${usuarioData.apellido};${usuarioData.nombre};;;
FN:${usuarioData.nombre} ${usuarioData.apellido}
ORG:${usuarioData.empresa}
TITLE:${usuarioData.puesto}
TEL:${usuarioData.telefono}
EMAIL:${usuarioData.correo_electronico}
END:VCARD
    `;

    imprimirEtiquetaQR(vCard)
}

$(document).ready(function() {
    // Filtra los resultados de la tabla en tiempo real según el texto ingresado en el campo de búsqueda
    $('#buscarUsuario').on('keyup', function() {
        const valorBusqueda = $(this).val().toLowerCase();
        
        $('#usuariosRegistrados tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valorBusqueda) > -1);
        });
    });
});
