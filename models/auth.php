<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        // Buscar usuario
        $query = "SELECT id, password_hash FROM api_users WHERE username = :username AND status = 'ACTIVE' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña y generar token
        if ($user && password_verify($password, $user['password_hash'])) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $queryToken = "INSERT INTO api_tokens SET user_id = :user_id, token = :token, expires_at = :expires_at";
            $stmtToken = $this->conn->prepare($queryToken);
            $stmtToken->bindParam(':user_id', $user['id']);
            $stmtToken->bindParam(':token', $token);
            $stmtToken->bindParam(':expires_at', $expires_at);

            if ($stmtToken->execute()) {
                return ['access_token' => $token, 'expires_at' => $expires_at];
            }
        }
        return false;
    }

    public function validateToken($token) {
        $query = "SELECT id FROM api_tokens WHERE token = :token AND expires_at > NOW() AND revoked = 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}