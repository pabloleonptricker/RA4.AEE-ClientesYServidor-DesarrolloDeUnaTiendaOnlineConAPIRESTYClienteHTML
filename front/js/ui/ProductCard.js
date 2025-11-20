//Clase para renderizar un producto en el DOM.
class ProductCard {
    /**
     * Crea un elemento DOM (tarjeta) para un producto.
     * @param {object} product - El objeto producto (id, name, price, image_url, etc.).
     * @returns {HTMLElement} La tarjeta del producto.
     */
    static create(product) {
        const card = document.createElement('div');
        card.className = 'product-card'; 
        card.setAttribute('data-id', product.id);

        // Contenedor principal de producto (puede ser un enlace a product.html)
        const productLink = document.createElement('a');
        productLink.href = `product.html?id=${product.id}`;
        productLink.className = 'product-link';
        
        // Imagen
        const image = document.createElement('img');
        image.src = product.image_url || 'assets/images/default.jpg';
        image.alt = product.name;
        productLink.appendChild(image);
        
        // Título
        const title = document.createElement('h3');
        title.textContent = product.name;
        productLink.appendChild(title);
        
        // Precio
        const price = document.createElement('p');
        price.className = 'product-price';
        price.textContent = `${product.price.toFixed(2)} €`;
        
        // Botón de Añadir al Carrito (Funcionalidad por implementar después)
        const addButton = document.createElement('button');
        addButton.textContent = 'Añadir al Carrito';
        addButton.className = 'add-to-cart-btn';
        addButton.dataset.productId = product.id; 

        card.appendChild(productLink);
        card.appendChild(price);
        card.appendChild(addButton);

        return card;
    }
}