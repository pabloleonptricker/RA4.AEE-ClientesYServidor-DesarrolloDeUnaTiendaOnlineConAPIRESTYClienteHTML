<?php

//Formateo de respuestas HTTP.


class ResponseHandler {
    
    /**
     * Envía una respuesta JSON con el código de estado HTTP.
     * @param array $data Los datos a devolver.
     * @param int $statusCode El código de estado HTTP.
     */
    public static function sendJsonResponse(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        // Esto es clave para las respuestas de la API
        http_response_code($statusCode); 
        echo json_encode($data);
        exit(); // Terminamos la ejecución tras enviar la respuesta
    }
    
    /**
     * Envía una respuesta de error.
     * @param string $message Mensaje de error.
     * @param int $statusCode Código de estado del error (ej. 401 Unauthorized, 400 Bad Request).
     */
    public static function sendErrorResponse(string $message, int $statusCode = 400): void {
        self::sendJsonResponse([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}

?>