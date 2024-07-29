<?php

namespace minichan;

use minichan\http;
use minichan\html;
use minichan\cache;
use minichan\db;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/php/http/router.php';
require __ROOT__ . '/php/html/renderer.php';
require __ROOT__ . '/php/cache/cache.php';
require __ROOT__ . '/php/db/connection.php';
require __ROOT__ . '/php/db/sql.php';

$cache = new cache\FileCache('main');
$router = new http\Router();

// $router->add_middleware(function ($vars) {
// 	echo "middleware 1\n";
// });

// $router->add_middleware(function ($vars) {
// 	echo "middleware 2\n";
// });

$router->add_route(HTTP_GET, '/', function ($vars) {
	$template = new html\Renderer(__ROOT__ . '/templates', [
		'foo' => 'bar',
	]);
	echo $template->render('test.phtml', [
		'bar' => 'foo'
	]);

	$sql1 = db\Sql::new()
		->insert('boards', ['name', 'desc', 'nsfw']);
	$sql2 = db\Sql::new()
		->select(['id', 'name', 'desc', 'nsfw'])
		->from(db\Sql::lit('boards'))
		->where(db\Sql::new()
			->gt('timestamp', time() - 1000)
	);
	
	echo $sql1->str() . "<br>";
	echo $sql2->str() . "<br>";

	// $dbc = new db\Connection('127.0.0.1', 'minichan_db', 'minichan_db_user', 'minichan_db_pass');
	
	// $dbc->transaction(function (\PDO $pdo) {
	// 	$sth = $pdo->prepare(db\Sql::new()
	// 		->select(['id', 'name'])
	// 		->from(db\Sql::new()->literal('boards'))
	// 		->get()
	// 	);
	// 	$sth->execute();
	// 	$boards = $sth->fetch();
	// 	print_r($boards);
	// });
});

// $router->add_route(HTTP_GET, '/test', function ($vars) {
// 	echo "test";
// });

// $router->add_route(HTTP_GET, '/foo/bar', function ($vars) {
// 	echo "foo bar";
// });

// $router->add_route(HTTP_GET, '/cache/:key/:val/set', function ($vars) use (&$cache) {
// 	$val = $cache->get($vars[':key']);
// 	if ($val != null) {
// 		echo "returned from cache: " . $vars[':key'] . '/' . $val;
// 	} else {
// 		$cache->set($vars[':key'], $vars[':val'], 5);
// 		echo "set in cache: " . $vars[':key'] . '/' . $vars[':val'];
// 	}
// });

// $router->add_route(HTTP_GET, '/cache/flush', function ($vars) use (&$cache) {
// 	$cache->flush();
// 	echo "flushed cache";
// });

// $router->add_route(HTTP_GET, '/test/query', function ($vars) {
	
// });

$match = $router->match_route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if ($match != null) {
	$match->exec();
}
