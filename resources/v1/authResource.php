<?php
require_once '../config/database.php';
require_once '../models/auth.php';

class AuthResource {
    public function login() {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->username) && !empty($data->password)) {
            $db = (new Database())->getConnection();
            $auth = new Auth($db);
            $result = $auth->login($data->username, $data->password);

            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Credenciales incorrectas"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}