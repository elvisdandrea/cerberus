<?php

/**
 * App Configuration and Startup File
 *
 * @author:  Elvis D'Andrea
 * @email:  elvis.vista@gmail.com
 */

/**
 * Directory Definition
 */
define('MAINURL',   $_SERVER['SERVER_NAME'] . dirname($_SERVER["PHP_SELF"]));
define('BASEDIR',     dirname($_SERVER['PHP_SELF']) . '/');
define('MAINDIR',   __DIR__);
define('APPDIR',    MAINDIR .   '/app');
define('IFCDIR',    APPDIR  .   '/ifc');
define('LIBDIR',    APPDIR  .   '/lib');
define('TPLDIR',    MAINDIR .   '/tpl');
define('MODDIR',    MAINDIR .   '/mod');

define('CSSURL',    MAINURL . '/res/css');
define('JSURL',     MAINURL . '/res/js');

/**
 * Development Enviroment
 */
define('ENVDEV', '1');

/**
 * Register Handler Functions
 */
require_once MAINDIR . '/handler.php';

/**
 * Register Core Class
 */
require_once APPDIR . '/core.php';

$core = new core();
$core->execute();

exit;