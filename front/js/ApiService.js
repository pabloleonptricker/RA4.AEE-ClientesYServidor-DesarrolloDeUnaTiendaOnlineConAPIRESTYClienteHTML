// ¡AJUSTA ESTA URL CON LA RUTA LARGA DE TU XAMPP SI ES NECESARIO!
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
    
    /**
     * Envía el carrito de compras al servidor para validación de precios y stock.
     * @param {Array<{id: number, quantity: number}>} cartItems - El carrito a validar.
     * @param {string} token - El JWT de autenticación.
     */
    static async checkout(cartItems, token) {
        const url = `${API_BASE_URL}/carrito.php`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Incluir el token JWT en el encabezado
                    'Authorization': `Bearer ${token}` 
                },
                body: JSON.stringify({ cart: cartItems }) // Enviar el carrito en la clave 'cart'
            });

            // Si la respuesta no es 200 (ok), lanzar error (ej. 403, 404, 409)
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Error del servidor: Código ${response.status}` }));
                throw new Error(errorData.message);
            }

            const data = await response.json(); 
            return data;

        } catch (error) {
            console.error('Error en el checkout:', error.message);
            throw new Error(`Error al procesar el carrito: ${error.message}`);
        }
    }
    
    /**
     * Llama al API para obtener productos vistos recientemente (solo el ID).
     * @param {Array<number>} productIds - Lista de IDs de productos vistos.
     * @param {string} token - El JWT de autenticación.
     */
    static async getRecentProducts(productIds, token) {
        // En un escenario real, esto debería ser un GET /productos/vistos?ids=...
        // Pero lo implementaremos con POST para mayor simplicidad y seguridad al pasar IDs.
        const url = `${API_BASE_URL}/productos_vistos.php`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}` 
                },
                body: JSON.stringify({ ids: productIds })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Error del servidor: Código ${response.status}` }));
                throw new Error(errorData.message);
            }

            const data = await response.json(); 
            return data.products; // Esperamos un array de productos
            
        } catch (error) {
            console.error('Error al obtener productos vistos:', error.message);
            throw new Error(`Error al obtener productos vistos: ${error.message}`);
        }
    }
}