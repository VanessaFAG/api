<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../core/Router.php';
require_once '../resources/v1/userResource.php';
require_once '../resources/v1/productResource.php';
require_once '../resources/v1/authResource.php';
require_once '../models/auth.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;

$router = new Router('v1', $basePath);
$userResource = new UserResource();
$productResource =  new ProductResource();
$authResource = new AuthResource();

// Rutas publicas
$router->addRoute('POST', '/login', [$authResource, 'login']);

// Logica de protección
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if(strpos($currentUri, '/login') === false){
    $headers = getallheaders();
    $token = null;

    if (isset($headers['Authorization'])){
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }


    $db = (new Database())->getConnection();
    $auth = new Auth($db);

    if(!$token || !$auth->validateToken($token)){
        http_response_code(401);
        echo json_encode(["message" => "Acceso no autorizado. Token invalido o innexistente."]);
    exit();
    }
}

// rutas ya protegidas por la aurorización
$router->addRoute('GET', '/users', [$userResource, 'index']);
$router->addRoute('GET', '/productos', [$productResource, 'index']);

$router->dispatch();
?>