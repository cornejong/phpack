#!/usr/bin/php
<?php declare(strict_types = 1);

/* Turn off read only */
@ini_set('phar.readonly', 'Off');

/* Map app structure */
Phar::mapPhar('self.phar');

define('IS_BUILD', true);
define('BUILD_ID', '{{build_id}}');
define('BUILD_TIME', '{{build_time}}');
define('BUILD_NUMBER', '{{build_number}}');
define('BUILD_TARGET', '{{build_target}}');

/* Require the initializing script */
require 'phar://self.phar/init.php';
/* Create the app and run it */
(new App\App)->run();

/* turn read only back on */
ini_set('phar.readonly', 'On');

__HALT_COMPILER();
