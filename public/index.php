<?php

namespace minichan;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/core/module.php';
require __ROOT__ . '/core/router.php';

$router = new core\Router();
$modules = ['board', 'manage'];

foreach ($modules as $module) {
	(require __ROOT__ . "/modules/{$module}/module.php")->register($router);
}

$match = $router->match_route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if ($match != null) {
	$match->exec();
}
