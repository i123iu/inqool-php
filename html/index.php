<?php

require_once 'logger.php';
require_once 'router.php';

require_once 'models/db.php';
require_once 'models/model.php';

require_once 'config/constants.php';


$logger = new Logger();

$db = new DB(DB_FILE_PATH, ['models/tables.sql', 'models/test-data.sql']);
$model = new Model($db, $logger);

$router = new Router($model, $logger);
$router->process_request($_SERVER['REQUEST_URI']);
