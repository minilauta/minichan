<?php

namespace minichan;

use minichan\http;
use minichan\cache;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/php/cache/cache.php';
require __ROOT__ . '/php/http/router.php';

$cache = new cache\FileCache('main');
$router = new http\Router();

$router->add_middleware(function ($vars) {
	echo "middleware 1\n";
});

$router->add_middleware(function ($vars) {
	echo "middleware 2\n";
});

$router->add_route(HTTP_GET, '/', function ($vars) {
	echo "root";
});

$router->add_route(HTTP_GET, '/test', function ($vars) {
	echo "test";
});

$router->add_route(HTTP_GET, '/foo/bar', function ($vars) {
	echo "foo bar";
});

$router->add_route(HTTP_GET, '/cache/:key/:val/set', function ($vars) use (&$cache) {

	$val = $cache->get($vars[':key']);
	if ($val != null) {
		echo "returned from cache: " . $vars[':key'] . '/' . $val;
	} else {
		$cache->set($vars[':key'], $vars[':val'], 5);
		echo "set in cache: " . $vars[':key'] . '/' . $vars[':val'];
	}
});

$router->add_route(HTTP_GET, '/cache/flush', function ($vars) use (&$cache) {
	$cache->flush();
	echo "flushed cache";
});

$match = $router->match_route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if ($match != null) {
	$match->exec();
}
