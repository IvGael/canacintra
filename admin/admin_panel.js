// Función para inicializar el escáner de QR automáticamente
function inicializarEscanerQR() {
    const html5QrCode = new Html5Qrcode("reader");

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start(
        { facingMode: "environment" }, // Usar la cámara trasera
        config,
        (decodedText, decodedResult) => {
            console.log(`Código QR escaneado: ${decodedText}`);
            document.getElementById('qrResult').innerHTML = `Código QR escaneado: ${decodedText}`;

            // Llamar a la función para procesar el código QR escaneado
            onQRCodeScanned(decodedText);
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
    console.log("Intentando imprimir etiqueta con datos: " + qrCodeData);

    let etiquetaXML = `
    <?xml version="1.0" encoding="utf-8"?>
    <DieCutLabel Version="8.0" Units="twips">
      <PaperOrientation>Landscape</PaperOrientation>
      <Id>Shipping</Id>
      <PaperName>30256 Shipping</PaperName>
      <DrawCommands/>
      <ObjectInfo>
        <BarcodeObject>
          <Name>QR</Name>
          <ForeColor Alpha="255" Red="0" Green="0" Blue="0"/>
          <BackColor Alpha="0" Red="255" Green="255" Blue="255"/>
          <Text>${qrCodeData}</Text>
          <Type>QRCode</Type>
          <Size>Large</Size>
          <TextPosition>None</TextPosition>
        </BarcodeObject>
        <Bounds X="0" Y="0" Width="6000" Height="3000"/>
      </ObjectInfo>
    </DieCutLabel>`;

    let label = dymo.label.framework.openLabelXml(etiquetaXML);
    
    if (!label) {
        console.error("Error al cargar la plantilla de etiqueta.");
        alert("No se pudo cargar la plantilla de la etiqueta.");
        return;
    }

    console.log("Etiqueta cargada correctamente.");

    // Obtener la lista de impresoras
    let printers = dymo.label.framework.getPrinters();
    if (printers.length === 0) {
        alert("No se encontraron impresoras.");
        return;
    }

    // Mostrar impresoras disponibles en la consola
    printers.forEach(function(printer) {
        console.log("Impresora disponible: " + printer.name);
    });

    let printer = printers.find(p => p.name.includes("DYMO LabelWriter 450 Turbo"));
    if (!printer) {
        alert("Impresora DYMO no disponible.");
        return;
    }

    console.log("Imprimiendo en impresora: " + printer.name);

    // Intentar realizar la impresión
    try {
        label.print(printer.name);
        console.log("Impresión iniciada.");
    } catch (error) {
        console.error("Error durante la impresión: ", error);
        alert("No se pudo completar la impresión.");
    }
}


// Función para verificar la lista de impresoras disponibles
function verificarImpresorasDisponibles() {
    let printers = dymo.label.framework.getPrinters();
    if (printers.length === 0) {
        alert("No se encontraron impresoras.");
        return;
    }

    printers.forEach(function(printer) {
        console.log("Impresora disponible: " + printer.name);
    });
}

// Función para marcar la asistencia y generar nuevo código QR
function onQRCodeScanned(qrCodeData) {
    $.ajax({
        url: 'marcar_asistencia.php', // Ruta al archivo PHP que actualiza la asistencia
        type: 'POST',
        data: { qrCodeData: qrCodeData }, // Enviar el código QR al servidor
        success: function(response) {
            if (response.success) {
                console.log('Asistencia marcada correctamente.');
                // Imprimir el nuevo código QR después de marcar la asistencia
                imprimirEtiquetaQR(qrCodeData);
                alert('Asistencia marcada y QR impreso.');
            } else {
                console.error('Error al marcar asistencia: ' + response.message);
                alert('Error al marcar asistencia: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al marcar asistencia: ', error);
            alert('Error en la solicitud al servidor.');
        }
    });
}

$(document).ready(function() {
    inicializarEscanerQR(); // Iniciar escáner QR
    verificarImpresorasDisponibles(); // Verificar si la impresora está disponible
});
