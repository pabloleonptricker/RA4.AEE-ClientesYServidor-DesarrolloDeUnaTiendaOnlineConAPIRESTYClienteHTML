// front/js/ApiService.js

// ¡AJUSTA ESTA URL CON LA RUTA LARGA DE TU XAMPP!
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

            // Si la respuesta no es 200, lanzar un error
            if (!response.ok) {
                // Intentar parsear el error como JSON, si no, lanzar un mensaje genérico
                const errorData = await response.json().catch(() => ({ message: `Error del servidor: Código ${response.status}` }));
                throw new Error(errorData.message);
            }

            // Aquí es donde ocurría el error si el servidor enviaba HTML
            const data = await response.json(); 
            return data;

        } catch (error) {
            console.error('Error en el login:', error.message);
            throw new Error(`Error en el login: ${error.message}`);
        }
    }
    
    // ... otros métodos de API (carrito.php, etc.)
}