<?php

// Maneja la autenticación y devuelve JWT y la tienda.

// Incluimos las clases necesarias desde la carpeta /src
// Usamos __DIR__ para asegurar rutas absolutas desde el script actual
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/AuthService.php';
require_once __DIR__ . '/../src/ResponseHandler.php';

use ResponseHandler as RH;

// 1. **Verificar Método HTTP**: Solo aceptamos POST para el login.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    RH::sendErrorResponse("Método no permitido.", 405);
}

// 2. **Recibir Credenciales**: Los datos vienen en el body de la petición (JSON).
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    RH::sendErrorResponse("Faltan credenciales (usuario o contraseña).", 400);
}

// 3. **Inicializar Servicios**
$db = new Database();
$authService = new AuthService($db);

// 4. **Autenticar Usuario**
$user = $authService->authenticateUser($username, $password);

if ($user === null) {
    RH::sendErrorResponse("Credenciales incorrectas.", 401);
}

// 5. **Éxito**: Generar JWT y obtener datos de la tienda
$jwt = $authService->generateJwt($user);
$storeData = $db->readJsonFile('tienda.json'); 

if ($storeData === null) {
    RH::sendErrorResponse("Error interno: No se pudo cargar la información de la tienda.", 500);
}

// 6. **Responder al Cliente** (Envía el token y los datos de la tienda)
RH::sendJsonResponse([
    'success' => true,
    'token' => $jwt,
    'storeData' => $storeData // Contiene 'categories' y 'products'
], 200);