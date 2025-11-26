<?php

//Gestiona la lista de productos vistos.
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/AuthService.php';
require_once __DIR__ . '/../src/ResponseHandler.php';

use ResponseHandler as RH;

// 1. **Verificar Método HTTP**: Solo aceptamos POST para pasar los IDs en el cuerpo de la solicitud.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RH::sendErrorResponse("Método no permitido.", 405);
}

// 2. **Verificar Autenticación**
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = preg_replace('/Bearer\s/', '', $authHeader);

$db = new Database();
$authService = new AuthService($db); 

if (!$authService->validateToken($token)) {
    RH::sendErrorResponse("Acceso denegado. Token no válido o ausente.", 403);
}

// 3. **Recibir la lista de IDs**
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// El cliente envía un array de IDs bajo la clave 'ids'
$productIds = $data['ids'] ?? []; 

if (empty($productIds) || !is_array($productIds)) {
    // Si no hay IDs o el formato es incorrecto, devolvemos una lista vacía con éxito.
    RH::sendJsonResponse(['products' => []], 200);
}

// 4. **Cargar Datos Maestros de la Tienda**
$storeData = $db->readJsonFile('tienda.json');

if ($storeData === null || !isset($storeData['products'])) {
    RH::sendErrorResponse("Error interno: No se pudo cargar el catálogo de productos.", 500);
}

// 5. **Filtrar productos solicitados**
$catalog = $storeData['products'];
$recentProducts = [];

foreach ($catalog as $product) {
    // Verificar si el ID del producto actual está en la lista de IDs solicitados por el cliente
    // Usamos in_array(producto.id, lista_de_ids_vistos) para la búsqueda eficiente
    if (in_array($product['id'], $productIds)) {
        
        // Incluir la información de la categoría para el frontend
        $category = array_filter($storeData['categories'], fn($cat) => $cat['id'] === $product['id_categoria']);
        $product['category_name'] = $category ? array_values($category)[0]['name'] : 'N/A';
        
        // Solo enviamos los datos necesarios.
        $recentProducts[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'formato' => $product['formato'],
            'stock' => $product['stock'],
            'image_url' => $product['image_url'],
            'category_name' => $product['category_name']
        ];
    }
}

// 6. **Respuesta de Éxito**
// Reordenamos los productos para que coincidan con el orden en que fueron vistos (opcional, pero buena práctica)
$orderedRecentProducts = [];
foreach ($productIds as $id) {
    $found = array_filter($recentProducts, fn($p) => $p['id'] === $id);
    if (!empty($found)) {
        $orderedRecentProducts[] = array_values($found)[0];
    }
}


RH::sendJsonResponse([
    'products' => $orderedRecentProducts
], 200);