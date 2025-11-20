<?php

class ResponseHandler {
    
    /**
     * Envía una respuesta JSON de éxito o error.
     * @param array $data Los datos a devolver.
     * @param int $statusCode Código HTTP.
     */
    public static function sendJsonResponse(array $data, int $statusCode = 200): void {
        // Establece el código de estado HTTP
        http_response_code($statusCode); 
        
        // Establece el encabezado de contenido como JSON
        header('Content-Type: application/json');
        
        // Imprime el JSON y termina la ejecución
        echo json_encode($data);
        exit();
    }

    /**
     * Envía una respuesta JSON de error con un mensaje.
     * @param string $message Mensaje de error.
     * @param int $statusCode Código HTTP de error.
     */
    public static function sendErrorResponse(string $message, int $statusCode = 400): void {
        self::sendJsonResponse([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}