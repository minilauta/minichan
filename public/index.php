<?php

namespace minichan;

define('__ROOT__', __DIR__ . '/../src');

require __ROOT__ . '/core/app.php';

$app = new core\App(['home', 'manage', 'board'], []);
$app->process_request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
