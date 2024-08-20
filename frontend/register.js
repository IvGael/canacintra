document.getElementById('registrationForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    try {
        const response = await fetch('../backend/register.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Error en la red. Código de estado: ' + response.status);
        }

        const data = await response.json();

        if (data.success) {
            alert('¡Registro exitoso! Revisa tu correo para el código QR.');
        } else {
            alert('Hubo un problema con tu registro: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Hubo un problema con tu registro. Detalles del error: ' + error.message);
    }
});
