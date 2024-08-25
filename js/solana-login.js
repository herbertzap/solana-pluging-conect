document.addEventListener('DOMContentLoaded', function() {
    const connectButton = document.getElementById('connect-wallet');

    if (connectButton) {
        connectButton.addEventListener('click', async function() {
            try {
                const provider = window.solana;
                if (provider && provider.isPhantom) {
                    const resp = await provider.connect();
                    const publicKey = resp.publicKey.toString();

                    // Mensaje que el usuario firmará
                    const message = 'Iniciar sesión en WordPress con Solana';
                    const encodedMessage = new TextEncoder().encode(message);

                    // Firma del mensaje
                    const signedMessage = await provider.request({
                        method: 'signMessage',
                        params: {
                            message: encodedMessage,
                            display: 'utf8', // Opcional, para mostrar el mensaje en Phantom
                        },
                    });

                    const signature = signedMessage.signature;
                    console.log('Firma:', signature);
                    console.log('Public Key:', publicKey);

                    // Guardar la clave pública en localStorage
                    localStorage.setItem('solanaPublicKey', publicKey);

                    // Enviar los datos al servidor para verificación
                    const response = await fetch('/new/wp-json/solana-login/v1/authenticate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            publicKey: publicKey,
                            signature: signature,
                            message: message,
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.href = result.redirect_url;
                    } else {
                        alert('Autenticación fallida: ' + result.message);
                    }
                } else {
                    alert('Solana wallet not found. Please install Phantom.');
                }
            } catch (err) {
                console.error('Error connecting to Solana wallet:', err);
            }
        });
    }
});
