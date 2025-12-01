<?php

//Recibe el carrito, valida precios y procesa el pedido.

// Incluimos las clases necesarias
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/AuthService.php';
require_once __DIR__ . '/../src/ResponseHandler.php';

use ResponseHandler as RH;

// 1. **Verificar Método HTTP**: Solo aceptamos POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RH::sendErrorResponse("Método no permitido.", 405);
}

// 2. **Verificar Autenticación** (Envío del Token JWT)
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = preg_replace('/Bearer\s/', '', $authHeader);

$db = new Database();
$authService = new AuthService($db); // Inicializar AuthService para validación
if (!$authService->validateToken($token)) {
    RH::sendErrorResponse("Acceso denegado. Token no válido o ausente.", 403);
}

// 3. **Recibir Datos del Carrito**
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$cartItems = $data['cart'] ?? [];

if (empty($cartItems)) {
    RH::sendErrorResponse("Carrito vacío. No hay productos para procesar.", 400);
}

// 4. **Cargar Datos Maestros de la Tienda**
$storeData = $db->readJsonFile('tienda.json');

if ($storeData === null || !isset($storeData['products'])) {
    RH::sendErrorResponse("Error interno: No se pudo cargar el catálogo de productos.", 500);
}

$catalog = [];
foreach ($storeData['products'] as $product) {
    // Mapear catálogo por ID para búsqueda rápida
    $catalog[$product['id']] = $product;
}

// 5. **Validación de Precios y Stock**
$totalPrice = 0;
$validatedCart = [];

foreach ($cartItems as $item) {
    $productId = $item['id'];
    $quantity = (int)$item['quantity'];

    if (!isset($catalog[$productId])) {
        // Producto no encontrado
        RH::sendErrorResponse("Error: Producto ID {$productId} no encontrado en el catálogo.", 404);
    }
    
    $product = $catalog[$productId];

    // Validar Stock
    if ($quantity <= 0) {
        RH::sendErrorResponse("Error: La cantidad para el producto '{$product['name']}' debe ser positiva.", 400);
    }
    if ($quantity > $product['stock']) {
        RH::sendErrorResponse("Error: Stock insuficiente para el producto '{$product['name']}' ({$product['formato']}). Máximo disponible: {$product['stock']}.", 409);
    }

    // Calcular precio basado en el precio del servidor (prevención de manipulación)
    $itemPrice = $product['price'] * $quantity;
    $totalPrice += $itemPrice;
    
    // Devolver ítems con información completa del servidor
    $validatedCart[] = [
        'id' => $productId,
        'name' => $product['name'],
        'quantity' => $quantity,
        'unit_price' => $product['price'],
        'subtotal' => round($itemPrice, 2)
    ];
}

// 6. **Respuesta de Éxito**
RH::sendJsonResponse([
    'success' => true,
    'message' => 'Pedido validado y completado con éxito.',
    'validated_items' => $validatedCart,
    'final_total' => round($totalPrice, 2)
], 200);
