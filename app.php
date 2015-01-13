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
define('MAINURL',   ( strpos($_SERVER['SERVER_NAME'], 'http://') !== false ? $_SERVER['SERVER_NAME'] :
        'http://' . $_SERVER['SERVER_NAME'] ) . dirname($_SERVER["PHP_SELF"]));

define('BASEDIR',     (dirname($_SERVER['PHP_SELF']) != '/' ? dirname($_SERVER['PHP_SELF']) . '/' : '/'));

define('MAINDIR',   __DIR__);
define('APPDIR',    MAINDIR .   '/app');
define('IFCDIR',    APPDIR  .   '/ifc');
define('LIBDIR',    APPDIR  .   '/lib');
define('TPLDIR',    MAINDIR .   '/tpl');
define('MODDIR',    MAINDIR .   '/mod');

define('CSSURL',    MAINURL . '/res/css');
define('JSURL',     MAINURL . '/res/js');

/**
 * Site Language
 */
define('LNG', 'pt');

/**
 * Development Enviroment
 */
define('ENVDEV', '0');

/**
 * If attends to ReSTful requests
 */
define('RESTFUL', '0');

/**
 * If ReSTful, which format we're working
 */
define('RESTFORMAT', 'json');


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