<?php

// max 1 day
define('MAX_RESERVATION_DURATION', 24 * 60);

// min 6 hours before deleting
define ('MIN_TIME_BEFORE_DELETE', 6 * 60);

define('DB_FILE_PATH', ($_ENV['DB_PATH'] ?? '../database/') . 'db.sqlite');
