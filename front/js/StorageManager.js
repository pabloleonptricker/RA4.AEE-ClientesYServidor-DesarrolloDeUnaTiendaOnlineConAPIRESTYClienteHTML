const StorageManager = (() => {
    const CART_KEY = 'shopping_cart';
    const AUTH_KEY = 'auth_token';
    const STORE_KEY = 'store_data';
    
    // --- Utilidades de Datos ---

    const getCart = () => {
        const cartJson = localStorage.getItem(CART_KEY);
        // Retorna un array vacío si no hay nada guardado
        return cartJson ? JSON.parse(cartJson) : [];
    };

    const saveCart = (cart) => {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
    };

    // --- Funciones de Autenticación y Tienda ---

    const saveAuthData = (token, storeData) => {
        localStorage.setItem(AUTH_KEY, token);
        localStorage.setItem(STORE_KEY, JSON.stringify(storeData));
    };

    const getToken = () => {
        return localStorage.getItem(AUTH_KEY);
    };
    
    const getStoreData = () => {
        const storeJson = localStorage.getItem(STORE_KEY);
        return storeJson ? JSON.parse(storeJson) : null;
    };
    
    const clearAuthData = () => {
        localStorage.removeItem(AUTH_KEY);
        localStorage.removeItem(STORE_KEY);
    };

    // --- Funciones del Carrito ---

    const addToCart = (productId) => {
        const cart = getCart();
        
        // Convertimos el ID a String y limpiamos espacios para la búsqueda robusta
        const cleanProductId = String(productId).trim();

        // Buscamos si el producto ya existe en el carrito
        const existingItem = cart.find(item => String(item.productId).trim() === cleanProductId);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            // Guardamos SOLO el ID del producto y la cantidad
            cart.push({
                productId: cleanProductId, // Guardamos el ID limpio
                quantity: 1,
            });
        }
        
        saveCart(cart);
        // Opcional: devolver la cantidad actual para un alert, si es necesario
        return existingItem ? existingItem.quantity : 1; 
    };
    
    const removeFromCart = (productId) => {
        let cart = getCart();
        
        const cleanProductId = String(productId).trim();
        
        // Filtramos y mantenemos todos los productos cuyo ID no coincida con el que queremos eliminar
        cart = cart.filter(item => String(item.productId).trim() !== cleanProductId);
        
        saveCart(cart);
    };
    
    const clearCart = () => {
        localStorage.removeItem(CART_KEY);
    };


    return {
        saveAuthData,
        getToken,
        getStoreData,
        clearAuthData,
        
        getCart,
        addToCart,
        removeFromCart,
        clearCart
    };
})();