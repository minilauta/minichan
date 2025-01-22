<?php

namespace minichan\modules;

use minichan\core;

require_once __ROOT__ . '/core/module.php';
require_once __ROOT__ . '/core/html_renderer.php';

class BoardModule implements core\Module
{
	public function __construct()
	{

	}

	public function __destruct()
	{

	}

	public function register(core\Router &$router): void
	{
		$router->add_route(HTTP_GET, '/board', function ($vars) {
			$renderer = new core\HtmlRenderer(__DIR__ . '/templates');
			echo $renderer->render('foobar.phtml', ['foo' => 'bar', 'bar' => 'foo']);
		});
	}
}

return new BoardModule();
