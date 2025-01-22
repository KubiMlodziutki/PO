<?php
require __DIR__ . '/../autoload.php';

$controllerName = $_GET['controller'] ?? 'product';
$actionName     = $_GET['action']     ?? 'list';

use App\Controller\ProductController;

switch ($controllerName) {
    case 'product':
    default:
        $controller = new ProductController();
        break;
}

$methodName = $actionName . 'Action';
if (!method_exists($controller, $methodName)) {
    die("Brak akcji: $actionName w kontrolerze: $controllerName");
}
$controller->$methodName();
