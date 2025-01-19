<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use App\Controller\OrderController;
use App\Database;
use App\Model\OrderModel;
use App\Router;

header('Content-Type: application/json');

$router = new Router();
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$OrderModel = new OrderModel($db->getConnection());

$router->addRoute('POST', '/orders', [OrderController::class, 'createNewOrder'], [$OrderModel]);
$router->addRoute('POST', '/orders/([0-9a-zA-Z_-]+)/items',[OrderController::class, 'updateOrders'], [$OrderModel]);
$router->addRoute('GET', '/orders/([0-9a-zA-Z_-]+)', [OrderController::class, 'getInfoOrders'], [$OrderModel]);
$router->addRoute('POST', '/orders/([0-9a-zA-Z_-]+)/done', [OrderController::class, 'orderUpdateDone'], [$OrderModel]);
$router->addRoute('GET', '/orders/', [OrderController::class, 'getAllOrders'], [$OrderModel]);

$route = $router->dispatch(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $_SERVER['REQUEST_METHOD']);

if ($route && isset($route['controller'], $route['method'])) {
	$controllerClass = $route['controller'];
	$method = $route['method'];
	$constructorParams = $route['constructorParams'];
	$methodParams = $route['methodParams'];

	if (class_exists($controllerClass)) {
		$controllerInstance = new $controllerClass(...$constructorParams);

		call_user_func_array([$controllerInstance, $method], $methodParams);
	} else {
		http_response_code(404);
		echo json_encode(['error' => 'Controller not found']);
	}
} else {
	http_response_code(404);
	echo json_encode(['error' => 'Not Found']);
}
