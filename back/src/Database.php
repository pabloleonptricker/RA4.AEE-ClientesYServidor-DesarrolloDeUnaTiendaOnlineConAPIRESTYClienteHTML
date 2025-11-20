<?php

//Manejo de archivos JSON de datos.

class Database {
    private $dataPath = __DIR__ . '/../data/';

    /**
     * Lee un archivo JSON del directorio /data y lo decodifica.
     * @param string $filename Nombre del archivo (ej: 'usuarios.json').
     * @return array|null Los datos decodificados o null si falla.
     */
    public function readJsonFile(string $filename): ?array {
        $filePath = $this->dataPath . $filename;

        if (!file_exists($filePath)) {
            // Manejo básico de errores: el archivo no existe
            error_log("Error: Archivo de datos no encontrado: " . $filePath);
            return null;
        }

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            // Error al leer el contenido
            error_log("Error: No se pudo leer el contenido del archivo: " . $filePath);
            return null;
        }

        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Error al decodificar JSON: " . json_last_error_msg());
            return null;
        }

        return $data;
    }
}

?>