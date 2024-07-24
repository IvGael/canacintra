document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('../backend/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Registro exitoso! Revisa tu correo para el código QR.');
        } else {
            alert('Hubo un problema con tu registro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un problema con tu registro. Inténtalo de nuevo.');
    });
});
