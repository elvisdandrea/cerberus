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
 * Some configuration
 */
define('LNG', 'pt');                    // Site Language
define('ENVDEV', '0');                  // Development Enviroment
define('RESTFUL', '0');                 // If attends to ReSTful requests
define('RESTFORMAT', 'json');           // If ReSTful, which format we're working
define('ENCRYPTURL', '0');              // If requests must run over encrypted URLs
define('ENCRYPT_POST_DATA', '0');       // If it should encrypt data sent through post

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