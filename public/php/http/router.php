<?php

namespace minichan\http;

define('HTTP_GET', 'GET');
define('HTTP_POST', 'POST');
define('HTTP_PUT', 'PUT');
define('HTTP_PATCH', 'PATCH');
define('HTTP_DELETE', 'DELETE');

define('ROUTER_URI_PARTS_MAX', 16);
define('ROUTER_URI_VAR_PREFIX', ':');
define('ROUTER_HANDLER_KEY', '!HANDLER');

class RouteMatch
{
	public array $vars;
	public array $middlewares;
	public $handler;

	public function __construct(array $vars, array &$middlewares, callable &$handler)
	{
		$this->vars = $vars;
		$this->middlewares = $middlewares;
		$this->handler = $handler;
	}

	public function exec(): void
	{
		foreach ($this->middlewares as &$middleware) {
			call_user_func($middleware, $this->vars);
		}
		call_user_func($this->handler, $this->vars);
	}
}

class Router
{
	private array $middlewares;
	private array $routes;

	public function __construct()
	{
		$this->middlewares = [];
		$this->routes = [];
	}

	public function add_middleware(callable $handler): void
	{
		$this->middlewares[] = $handler;
	}

	public function add_route(string $method, string $uri, callable $handler): void
	{
		if (!isset($this->routes[$method])) {
			$this->routes[$method] = [];
		}

		$uri_parts = Router::parse_uri($uri);
		$uri_parts_n = count($uri_parts);
		$route_part = &$this->routes[$method];
		foreach ($uri_parts as $idx => &$val) {
			if (!isset($route_part[$val])) {
				$route_part[$val] = [];
			}

			$route_part = &$route_part[$val];
			if ($idx === $uri_parts_n - 1) {
				$route_part[ROUTER_HANDLER_KEY] = $handler;
			}
		}
	}

	public function match_route(string $method, string $uri): ?RouteMatch
	{
		if (!isset($this->routes[$method])) {
			return null;
		}

		$uri_parts = Router::parse_uri($uri);
		$uri_parts_n = count($uri_parts);
		$route_part = &$this->routes[$method];
		$match_vars = [];
		$match_handler = null;
		foreach ($uri_parts as $idx => &$val) {
			if (isset($route_part[$val])) {
				$route_part = &$route_part[$val];
				if ($idx === $uri_parts_n - 1) {
					if (!empty($route_part) && isset($route_part[ROUTER_HANDLER_KEY])) {
						$match_handler = &$route_part[ROUTER_HANDLER_KEY];
					}
				}
			} else if (!empty($route_part)) {
				foreach ($route_part as $p_key => &$p_val) {
					if (!is_string($p_key)) {
						continue;
					} else if (strlen($p_key) === 0) {
						continue;
					}

					if ($p_key[0] === ROUTER_URI_VAR_PREFIX) {
						$match_vars[$p_key] = $val;
						$route_part = $p_val;
						break;
					}
				}
			}
		}

		if ($match_handler == null) {
			return null;
		}

		return new RouteMatch($match_vars, $this->middlewares, $match_handler);
	}

	public static function parse_uri(string $uri): array
	{
		return explode('/', strtolower(rtrim(explode('?', $uri)[0], '/')), ROUTER_URI_PARTS_MAX);
	}
}
