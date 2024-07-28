<?php

namespace minichan;

use minichan\http;
use minichan\cache;
use minichan\db;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/php/http/router.php';
require __ROOT__ . '/php/cache/cache.php';
require __ROOT__ . '/php/db/db.php';

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

$router->add_route(HTTP_GET, '/test/query', function ($vars) {
	$sql = db\Sql::new()
		->select([
			'MAX(sq1.total_posts) AS total_posts',
			'MAX(sq1.current_posts) AS current_posts',
			'MAX(sq1.unique_posters) AS unique_posters',
			'MAX(sq1.imported_posts) AS imported_posts',
			'MAX(sq1.current_files) AS current_files',
			'MAX(sq1.active_content) AS active_content',
		])
		->from(db\Sql::new()
			->select([
				'SUM(sq11.total_posts) AS total_posts',
				'NULL AS current_posts',
				'NULL AS unique_posters',
				'NULL AS imported_posts',
				'NULL AS current_files',
				'NULL AS active_content',
			])
			->from(db\Sql::new()
				->select(['board_id AS board_id', 'MAX(post_id) AS total_posts'])
				->from(db\Sql::new()->literal('posts'))
			, 'sq11')

			->op('UNION ALL')

			->select([
				'NULL AS total_posts',
				'COUNT(*) AS current_posts',
				'NULL AS unique_posters',
				'NULL AS imported_posts',
				'NULL AS current_files',
				'NULL AS active_content',
			])
			->from(db\Sql::new()->literal('posts'))
		, 'sq1');
	echo $sql->get();
});

$match = $router->match_route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if ($match != null) {
	$match->exec();
}
