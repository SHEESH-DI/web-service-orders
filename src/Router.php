<?php

namespace App;

class Router
{
	private array $routes = [];

	public function addRoute($method, $path, $handler, $params = []): void
	{
		$this->routes[] = [
			'method' => $method,
			'path' => $path,
			'handler' => $handler,
			'params' => $params,
		];
	}

	public function dispatch(string $path, string $method): array
	{
		foreach ($this->routes as $route) {
			if ($route['method'] === $method && preg_match('#^' . $route['path'] . '$#', $path, $matches)) {
				array_shift($matches);

				if ($_GET) {
					parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queryParams);
					$methodParams = array_merge($matches, $queryParams);
				}

				return [
					'controller' => $route['handler'][0],
					'method' => $route['handler'][1],
					'constructorParams' => $route['params'],
					'methodParams' => $methodParams ?? $matches,
				];
			}
		}
		return [];
	}
}
