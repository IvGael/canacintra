// Función para inicializar el escáner de QR automáticamente
function inicializarEscanerQR() {
    const html5QrCode = new Html5Qrcode("reader");

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start(
        { facingMode: "environment" }, // Usar la cámara trasera
        config,
        (decodedText, decodedResult) => {
            console.log(`Código QR escaneado: \n${decodedText}`);
            document.getElementById('qrResult').innerHTML = `Código QR escaneado: \n${decodedText}`;

            // Llamar a la función para procesar el código QR escaneado
            setTimeout(() => { console.log('World!') }, 4000)
            imprimirEtiquetaQR(decodedText);
            setTimeout(() => { console.log('World!') }, 4000)
        },
        (errorMessage) => {
            console.warn(`Error en el escaneo: ${errorMessage}`);
        }
    ).catch((err) => {
        console.error("Error al acceder a la cámara: ", err);
        alert("No se pudo acceder a la cámara.");
    });
}

function imprimirEtiquetaQR(qrCodeData) {
    console.log("hey 1")
    const { jsPDF } = window.jspdf;
    
    const pageWidth = 101.6;
    const pageHeight = 53.97;
    const QRSize = 45;

    // Get user inputs
    console.log("hey 2")
    const fnMatch = qrCodeData.match(/FN:(.*)/);
    const name = fnMatch ? fnMatch[1].trim() : "Unknown";
    const qrData = qrCodeData;

    // Generate QR code
    const qr = new QRious({
        value: qrData,
        size: 150
    });

    console.log("hey 3")
    // Create PDF
    const pdf = new jsPDF("landscape", "mm", [pageHeight, pageWidth]); // Set custom dimensions in mm

    console.log("hey 4")
    // Add name text
    pdf.setFontSize(16);
    const textWidth = pdf.getTextWidth(name);
    const xCordinate = (pageWidth - textWidth) / 2;
    pdf.text(name, xCordinate, 9); // Center the name text at the top

    console.log("hey 5")
    
    // Add QR code
    const qrDataUrl = qr.toDataURL();
    const xQRCordinate = (pageWidth - QRSize) / 2;
    pdf.addImage(qrDataUrl, "PNG", xQRCordinate, 11, QRSize, QRSize); // Position and size for QR code

    // Create a new window to print the PDF
    const pdfOutput = pdf.output('blob'); // Create a Blob from the PDF
    const pdfUrl = URL.createObjectURL(pdfOutput); // Create a URL for the Blob

    
    // Open the PDF in a new window
    const printWindow = window.open(pdfUrl);

    // Wait for the PDF to load and then trigger print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close(); // Close the print window after printing
        };
    };
}

$(document).ready(function() {
    inicializarEscanerQR(); // Iniciar escáner QR
});

// Cargar y manejar usuarios registrados
$(document).ready(function() {
    cargarUsuariosRegistrados();

    $('#registroUsuario').on('submit', function(event) {
        event.preventDefault();

        const datosUsuario = {
            nombre: $('#nombre').val(),
            apellido: $('#apellido').val(),
            correo: $('#correo').val(),
            telefono: $('#telefono').val(),
            empresa: $('#empresa').val(),
            puesto: $('#puesto').val()
        };

        $.ajax({
            url: 'admin_registro_usuario.php',
            type: 'POST',
            data: datosUsuario,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    cargarUsuariosRegistrados();
                    $('#registroUsuario')[0].reset();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                alert('Error en la solicitud. Verifica la consola para más detalles.');
            }
        });
    });

    $('#buscarUsuario').on('keyup', function() {
        const valorBusqueda = $(this).val().toLowerCase();
        $('#usuariosRegistrados tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valorBusqueda) > -1);
        });
    });
});

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
                            <td><button class="btn btn-danger eliminar-usuario" data-id="${usuario.id}">Eliminar</button></td>
                        </tr>`;
            });
            $('#usuariosRegistrados').html(rows);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar los usuarios:', error);
            alert('Error al cargar los usuarios. Verifica la consola para más detalles.');
        }
    });
}
