//Gestiona el localStorage (Token, Tienda, Carrito, Vistos) .

class StorageManager {
    // Claves que usaremos en localStorage
    static KEYS = {
        TOKEN: 'authToken',
        STORE_DATA: 'storeData', // Contiene productos y categorías [cite: 118]
        CART: 'shoppingCart',    // El carrito de compras [cite: 119]
        PRODUCTS_VIEWED: 'productsViewed' // Productos vistos recientemente [cite: 120]
    };

    /**
     * Guarda el token de autenticación y los datos de la tienda.
     * @param {string} token - El JWT recibido del servidor.
     * @param {object} storeData - El JSON completo de la tienda.
     */
    static saveAuthData(token, storeData) {
        if (token) {
            localStorage.setItem(StorageManager.KEYS.TOKEN, token); // Almacenar Token [cite: 117]
        }
        if (storeData) {
            localStorage.setItem(StorageManager.KEYS.STORE_DATA, JSON.stringify(storeData));
        }
    }

    /**
     * Recupera el token. Útil para verificar autenticación y para el ApiService.
     * @returns {string | null} El token o null.
     */
    static getToken() {
        return localStorage.getItem(StorageManager.KEYS.TOKEN);
    }

    /**
     * Carga los datos de la tienda desde localStorage (para Dashboard, Categorías, Producto)[cite: 173].
     * @returns {object | null} Los datos de la tienda o null.
     */
    static getStoreData() {
        const data = localStorage.getItem(StorageManager.KEYS.STORE_DATA);
        return data ? JSON.parse(data) : null;
    }

    /**
     * Elimina todos los datos de sesión (necesario para el Cierre de Sesión)[cite: 134].
     */
    static clearAll() {
        localStorage.removeItem(StorageManager.KEYS.TOKEN);
        localStorage.removeItem(StorageManager.KEYS.STORE_DATA);
        localStorage.removeItem(StorageManager.KEYS.CART);
        localStorage.removeItem(StorageManager.KEYS.PRODUCTS_VIEWED);
    }
}