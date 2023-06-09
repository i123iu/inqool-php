<?php

require_once 'logger.php';
require_once 'router.php';

require_once 'models/db.php';
require_once 'models/model.php';


// max 1 day
define('MAX_RESERVATION_DURATION', 24 * 60);


$logger = new Logger();

$db = new DB('../database/db.sqlite', ['models/tables.sql', 'models/test-data.sql']);
$model = new Model($db, $logger);

$router = new Router($model, $logger);
$router->process_request($_SERVER['REQUEST_URI']);
