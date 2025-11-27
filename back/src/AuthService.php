<?php

// Incluir la clase Database, ya que AuthService la necesita.
require_once __DIR__ . '/Database.php';

// CLAVE SECRETA para la firma del token (¡MUY IMPORTANTE!)
define('JWT_SECRET', 'ESTA_DEBE_SER_UNA_CLAVE_LARGA_Y_COMPLEJA_Y_SECRETA_123456'); 

class AuthService {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Verifica las credenciales del usuario.
     * @return array|null El usuario si es válido, o null si las credenciales son incorrectas.
     */
    public function authenticateUser(string $username, string $password): ?array {
        $users = $this->db->readJsonFile('usuarios.json');

        if ($users === null) {
            return null; // No se pudieron cargar los usuarios.
        }

        foreach ($users['users'] as $user) { // Asumiendo que el JSON tiene una clave 'users'
            // En un sistema real, la contraseña estaría hasheada
            if ($user['username'] === $username && $user['password'] === $password) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Genera un token JWT simple (simulación).
     * @param array $user Datos del usuario.
     * @return string El token generado.
     */
    public function generateJwt(array $user): string {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'exp' => time() + (60 * 60) // Expira en 1 hora
        ];

        // Simplificación: Unir los datos para simular un token.
        return base64_encode(json_encode($header)) . '.' . 
               base64_encode(json_encode($payload)) . '.' . 
               md5(JWT_SECRET); // Usar una firma simple
    }

    /**
     * Valida la firma del token (simulación de validación de JWT).
     * @param string $token El token a validar.
     * @return bool True si el token es válido, false en caso contrario.
     */
    public function validateToken(string $token): bool {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        $signature = $parts[2];
        
        // Simulación: verificamos que la firma coincida con la firma esperada.
        return $signature === md5(JWT_SECRET);
    }
}