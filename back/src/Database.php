<?php

class Database {
    
    // La ruta a la carpeta 'data' es relativa a la ubicación de Database.php (en /src)
    private const DATA_PATH = __DIR__ . '/../data/';

    /**
     * Lee y decodifica un archivo JSON.
     * @param string $filename Nombre del archivo (ej. 'usuarios.json').
     * @return array|null Contenido del JSON como array asociativo o null si falla.
     */
    public function readJsonFile(string $filename): ?array {
        $path = self::DATA_PATH . $filename;
        
        if (!file_exists($path)) {
            // En un entorno de producción, esto debería registrarse, no imprimir en la salida
            // error_log("Error: Archivo no encontrado en $path"); 
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            // error_log("Error: No se pudo leer el archivo $path");
            return null;
        }
        
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // error_log("Error de JSON: " . json_last_error_msg());
            return null;
        }

        return $data;
    }
}