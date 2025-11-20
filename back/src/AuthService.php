<?php

//Lógica de seguridad: Generación y Verificación de JWT.

// Incluimos Database, ya que AuthService la necesita para leer usuarios
require_once 'Database.php';

// Clave secreta para firmar el JWT. ¡IMPORTANTE! Usar una clave fuerte.
define('JWT_SECRET', 'TU_CLAVE_SECRETA_DEBE_SER_MUY_LARGA_Y_COMPLEJA'); 

class AuthService {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Verifica las credenciales contra el archivo usuarios.json.
     * @param string $username
     * @param string $password
     * @return array|null Usuario autenticado (sin password) o null si falla.
     */
    public function authenticateUser(string $username, string $password): ?array {
        $userData = $this->db->readJsonFile('usuarios.json');
        
        if ($userData === null || !isset($userData['users'])) {
            return null; // Error de lectura o formato de datos
        }

        foreach ($userData['users'] as $user) {
            if ($user['username'] === $username) {
                // **Validación de Contraseña**: Asumimos password plano.
                // En un entorno real, usar 'password_verify($password, $user['password_hash'])'
                if ($user['password'] === $password) {
                    // Eliminamos la contraseña del objeto usuario antes de devolverlo
                    unset($user['password']); 
                    return $user;
                }
            }
        }

        return null; // Autenticación fallida
    }

    /**
     * Genera un JSON Web Token (JWT) para el usuario.
     * El Payload incluye los datos del usuario y la expiración.
     * @param array $user Datos del usuario (id, username, role).
     * @return string El token JWT.
     */
    public function generateJwt(array $user): string {
        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 60); // Token válido por 1 hora
        
        // PAYLOAD (Carga útil): Datos del token
        $payload = [
            'iat'  => $issuedAt,              // Issued At: Creado en
            'exp'  => $expirationTime,        // Expiration Time: Expira en
            'iss'  => 'tienda-online-api',    // Issuer: Emisor
            'data' => [                       // Datos del usuario
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ];

        // Header (Tipo y algoritmo)
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payloadEncoded = base64_encode(json_encode($payload));
        
        // Signature (Firma) usando la clave secreta
        $signature = hash_hmac('sha256', "$header.$payloadEncoded", JWT_SECRET, true);
        $signatureEncoded = base64_encode($signature);
        
        // Estructura final: Header.Payload.Signature
        return "$header.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Valida un JWT. Necesario para todos los demás endpoints.
     * La implementación debe verificar la firma y la expiración.
     * @param string $jwt El token a validar.
     * @return array|null Datos del usuario si el token es válido, null si falla.
     */
    public function validateJwt(string $jwt): ?array {
        // ... (La implementación de esta función se requiere en carrito.php y productos_vistos.php)
        // Por ahora, solo es necesario implementarla para que los otros endpoints
        // puedan validar el token. Su lógica es la inversa de generateJwt.
        
        // [Implementación similar a la parte de generación, pero verificando la firma y 'exp']
        // ...
        return null; // Placeholder
    }
}

?>