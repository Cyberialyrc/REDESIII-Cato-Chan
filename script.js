document.addEventListener("DOMContentLoaded", function () {
    // Clave para cifrado AES (ejemplo, reemplazar con una más segura en producción)
    const ENCRYPTION_KEY = "12345678901234567890123456789012"; // 32 caracteres para AES-256

    // Función para cifrar con AES (manteniendo la lógica original)
    async function cifrarAES(data) {
        const iv = crypto.getRandomValues(new Uint8Array(16)); // Generar IV aleatorio
        const encoder = new TextEncoder();
        const key = await crypto.subtle.importKey('raw', encoder.encode(ENCRYPTION_KEY), 'AES-CBC', false, ['encrypt']);
        const dataBytes = encoder.encode(data);

        const encrypted = await crypto.subtle.encrypt({ name: 'AES-CBC', iv: iv }, key, dataBytes);
        const encryptedArray = new Uint8Array(encrypted);
        // Convertir el IV y el texto cifrado a un formato base64
        const encryptedData = [...iv, ...encryptedArray];
        return btoa(String.fromCharCode(...encryptedData));
    }

    // Función para mostrar un mensaje
    function mostrarMensaje(mensaje) {
        alert(mensaje); // Muestra el mensaje como una alerta
    }

    // Función de inicio de sesión
    async function login(event) {
        event.preventDefault();
    
        const usuario = document.getElementById('usuario').value.trim();
        const password = document.getElementById('password').value; // Usa la contraseña en texto plano
    
        if (!usuario || !password) {
            mostrarMensaje('Por favor, complete todos los campos.');
            return;
        }
    
        try {
            const response = await fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ usuario, password }) // Enviar la contraseña sin cifrar
            });
    
            // Verifica si la respuesta es exitosa
            if (!response.ok) {
                const errorResponse = await response.json();
                throw new Error(errorResponse.error || 'Error en la respuesta del servidor');
            }
    
            const data = await response.json();
            const mensajeLogin = document.getElementById('mensaje-login');
    
            if (data.error) {
                mensajeLogin.textContent = data.error;
                mensajeLogin.style.color = 'red';
                mostrarMensaje(data.error); // Mostrar mensaje de error
            } else {
                mensajeLogin.textContent = data.message;
                mensajeLogin.style.color = 'green';
                setTimeout(() => window.location.href = 'dashboard.html', 2000); // Redirigir tras éxito
                mostrarMensaje(data.message); // Mostrar mensaje de éxito
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            mostrarMensaje('Hubo un problema con la solicitud. Verifica la consola para más detalles.');
        }
    }

    // Asignar eventos de submit e interacciones a los botones
    document.getElementById('form-login').addEventListener('submit', login); // Asegúrate de que este evento se esté llamando correctamente

    const btnIngresar = document.getElementById('btn-ingresar');
    const btnRegistrar = document.getElementById('btn-registrar');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');

    btnIngresar.addEventListener('click', () => {
        formLogin.classList.remove('hidden');
        formRegister.classList.add('hidden');
    });

    btnRegistrar.addEventListener('click', () => {
        formRegister.classList.remove('hidden');
        formLogin.classList.add('hidden');
    });
});
