document.addEventListener("DOMContentLoaded", function () {
    // Función para mostrar el mensaje de alerta
    function mostrarMensaje(mensaje) {
        alert(mensaje); // Muestra el mensaje como una alerta
    }

    // Función de inicio de sesión
    async function login(event) {
        event.preventDefault();

        const usuario = document.getElementById('usuario').value;
        const password = document.getElementById('password').value;

        if (!usuario || !password) {
            mostrarMensaje('Por favor, complete todos los campos.');
            return;
        }

        const passwordCifrada = await encryptWithPublicKey(password); // Cifrado

        try {
            const response = await fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ usuario, password: passwordCifrada })
            });

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
            mostrarMensaje('Hubo un problema con la solicitud.');
        }
    }

    // Asignar evento de submit al formulario de login
    document.getElementById('form-login').addEventListener('submit', login); // Asignamos evento

    // Función para cargar la clave pública desde el archivo public_key.pem
    async function loadPublicKey() {
        const response = await fetch('/public_key.pem'); // Carga la clave pública
        const publicKeyPem = await response.text(); // Convierte a texto

        // Convertir la clave pública en formato PEM a ArrayBuffer
        const publicKeyData = pemABytes(publicKeyPem);

        // Importar la clave pública usando la Web Crypto API
        return window.crypto.subtle.importKey(
            'spki',
            publicKeyData,
            { name: 'RSA-OAEP', hash: 'SHA-256' },
            true,
            ['encrypt']
        );
    }

    // Función para cifrar la contraseña usando la clave pública
    async function encryptWithPublicKey(data) {
        const publicKey = await loadPublicKey(); // Carga la clave pública
        const encoder = new TextEncoder(); // Convierte el texto a bytes

        // Cifrar los datos (contraseña)
        const encrypted = await window.crypto.subtle.encrypt(
            { name: 'RSA-OAEP' },
            publicKey,
            encoder.encode(data)
        );

        // Convertir el ArrayBuffer cifrado a Base64 para enviarlo al servidor
        return btoa(String.fromCharCode(...new Uint8Array(encrypted)));
    }

    // Función auxiliar para convertir una clave PEM a ArrayBuffer
    function pemABytes(pem) {
        const b64 = pem
            .replace('-----BEGIN PUBLIC KEY-----', '')
            .replace('-----END PUBLIC KEY-----', '')
            .replace(/\s/g, '');
        const raw = atob(b64);
        const bytes = new Uint8Array(raw.length);
        for (let i = 0; i < raw.length; i++) {
            bytes[i] = raw.charCodeAt(i);
        }
        return bytes.buffer;
    }

    // Asignar eventos a los botones de login/registro
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
