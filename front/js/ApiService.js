const API_BASE_URL = 'http://localhost/RA4.AEE-ClientesYServidor-DesarrolloDeUnaTiendaOnlineConAPIRESTYClienteHTML/back/api';

class ApiService {

    static async login(username, password) {
        const url = `${API_BASE_URL}/login.php`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Error del servidor: Código ${response.status}` }));
                throw new Error(errorData.message);
            }

            const data = await response.json();
            return data;

        } catch (error) {
            console.error('Error en el login:', error.message);
            throw new Error(`Error en el login: ${error.message}`);
        }
    }

    static async checkout(cartItems, token) {
        // Enviar en la clave 'cart' porque carrito.php lee $data['cart']
        const url = `${API_BASE_URL}/carrito.php`;
        console.log('[ApiService.checkout] URL ->', url);
        console.log('[ApiService.checkout] Payload ->', cartItems);
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ cart: cartItems })
            });

            console.log('[ApiService.checkout] status:', response.status);
            const text = await response.text().catch(() => '');
            if (!response.ok) {
                console.error('[ApiService.checkout] response text:', text);
                const err = (() => { try { return JSON.parse(text).message || text; } catch { return text || `HTTP ${response.status}`; } })();
                throw new Error(err);
            }

            return JSON.parse(text || '{}');
        } catch (error) {
            console.error('Error en checkout:', error.message);
            throw new Error(`Error en checkout: ${error.message}`);
        }
    }
}
