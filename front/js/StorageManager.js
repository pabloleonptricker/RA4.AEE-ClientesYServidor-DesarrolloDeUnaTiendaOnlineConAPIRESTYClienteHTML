class StorageManager {
    // Claves que usaremos en localStorage
    static KEYS = {
        TOKEN: 'authToken',
        STORE_DATA: 'storeData', 
        CART: 'shoppingCart',    
        PRODUCTS_VIEWED: 'productsViewed' 
    };

    // --- Métodos de Sesión y Datos de Tienda ---

    static saveAuthData(token, storeData) {
        if (token) {
            localStorage.setItem(StorageManager.KEYS.TOKEN, token);
        }
        if (storeData) {
            localStorage.setItem(StorageManager.KEYS.STORE_DATA, JSON.stringify(storeData));
        }
    }

    static getToken() {
        return localStorage.getItem(StorageManager.KEYS.TOKEN);
    }

    static getStoreData() {
        const data = localStorage.getItem(StorageManager.KEYS.STORE_DATA);
        return data ? JSON.parse(data) : null;
    }

    static clearAll() {
        localStorage.removeItem(StorageManager.KEYS.TOKEN);
        localStorage.removeItem(StorageManager.KEYS.STORE_DATA);
        StorageManager.clearCart(); 
        StorageManager.clearProductsViewed(); 
    }
    
    // -----------------------------------------------------------------
    // --- MÉTODOS PARA EL CARRITO DE COMPRAS ---
    // -----------------------------------------------------------------

    /**
     * Obtiene el carrito del localStorage.
     * @returns {Array<{id: number, quantity: number}>} Lista de ítems del carrito.
     */
    static getCart() {
        const cart = localStorage.getItem(StorageManager.KEYS.CART);
        return cart ? JSON.parse(cart) : [];
    }

    /**
     * Guarda el array del carrito en localStorage.
     * @param {Array<object>} cartItems 
     */
    static saveCart(cartItems) {
        localStorage.setItem(StorageManager.KEYS.CART, JSON.stringify(cartItems));
    }

    /**
     * Añade un producto al carrito o incrementa su cantidad si ya existe.
     * @param {number} productId - El ID del producto.
     * @param {number} [quantity=1] - Cantidad a añadir.
     */
    static addToCart(productId, quantity = 1) {
        const cart = StorageManager.getCart();
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ id: productId, quantity: quantity });
        }

        StorageManager.saveCart(cart);
    }

    /**
     * Elimina completamente un producto del carrito.
     * @param {number} productId - El ID del producto a eliminar.
     */
    static removeFromCart(productId) {
        let cart = StorageManager.getCart();
        cart = cart.filter(item => item.id !== productId);
        StorageManager.saveCart(cart);
    }
    
    /**
     * Vacía completamente el carrito.
     */
    static clearCart() {
        localStorage.removeItem(StorageManager.KEYS.CART);
    }
    
    // -----------------------------------------------------------------
    // --- MÉTODOS PARA PRODUCTOS VISTOS RECIENTEMENTE (Futuro) ---
    // -----------------------------------------------------------------

    /**
     * Añade un producto a la lista de vistos recientemente.
     * @param {number} productId - El ID del producto.
     */
    static addProductViewed(productId) {
        let viewed = StorageManager.getProductsViewed();
        
        // Eliminar si ya existe para moverlo al inicio
        viewed = viewed.filter(id => id !== productId); 
        
        // Añadir al principio
        viewed.unshift(productId); 
        
        // Limitar a los últimos 5 productos vistos (por ejemplo)
        viewed = viewed.slice(0, 5); 
        
        localStorage.setItem(StorageManager.KEYS.PRODUCTS_VIEWED, JSON.stringify(viewed));
    }

    /**
     * Obtiene la lista de IDs de productos vistos recientemente.
     * @returns {Array<number>}
     */
    static getProductsViewed() {
        const viewed = localStorage.getItem(StorageManager.KEYS.PRODUCTS_VIEWED);
        return viewed ? JSON.parse(viewed) : [];
    }
    
    /**
     * Vacía la lista de productos vistos.
     */
    static clearProductsViewed() {
        localStorage.removeItem(StorageManager.KEYS.PRODUCTS_VIEWED);
    }
}