<?php

namespace minichan;

require __DIR__ . '/bootstrap.php';

require __ROOT__ . '/core/app.php';

$app = new core\App(['board', 'manage'], []);
$app->process_request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
