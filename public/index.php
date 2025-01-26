<?php

namespace minichan;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/core/app.php';

$app = new core\App(['home', 'manage', 'board'], []);
$app->process_request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
