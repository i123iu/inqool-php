<?php

require_once 'logger.php';
require_once 'router.php';

require_once 'models/db.php';
require_once 'models/model.php';


// max 1 day
define('MAX_RESERVATION_DURATION', 24 * 60);


$logger = new Logger();

$db_path = ($_ENV['DB_PATH'] ?? '../database/') . 'db.sqlite';
$db = new DB($db_path, ['models/tables.sql', 'models/test-data.sql']);
$model = new Model($db, $logger);

$router = new Router($model, $logger);
$router->process_request($_SERVER['REQUEST_URI']);
