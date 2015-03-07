<?php

/**
 * App Configuration and Startup File
 *
 * This is the begging of the magic.
 *
 * First things first: this will precisely guarantee that
 * the system functionality is smooth independently where you place it.
 *
 * In other words: no hard-coded directories
 * (no need for configuration xml/yml files or anything)
 *
 * Simply move your code, deploy, pull from git, whatever, and everything keeps
 * working with no problem. URLs will be automatically identified and files will be
 * automatically located and loaded. Everything works with no effort.
 *
 * The startup will then load the core class that handles the request.
 * Everything goes straight to what's important and only what's important is loaded.
 * It's fine tuned to do it simple and efficient, so it's not gonna rape the server like there's no tomorrow.
 *
 * It's also engineered to handle the server process automatically, so you just
 * code your action without having to include/require any file, handle errors or
 * create an over-engineered process that's for nothing other than create shitty stuff.
 *
 * @author: Elvis D'Andrea
 * @email:  <elvis.vista@gmail.com>
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
define('KRNDIR',    APPDIR  .   '/krn');
define('LIBDIR',    APPDIR  .   '/lib');
define('TPLDIR',    MAINDIR .   '/tpl');
define('MODDIR',    MAINDIR .   '/mod');

define('RESURL',    MAINURL . '/res');
define('CSSURL',    MAINURL . '/res/css');
define('JSURL',     MAINURL . '/res/js');
define('IMGURL',    MAINURL . '/res/img');

/**
 * Register Core Class
 */
require_once APPDIR . '/core.php';

/**
 * Register Handler Functions
 */
require_once MAINDIR . '/handler.php';

/**
 * Some configuration
 */
define('HOME', 'home');                 // Home Sweet Home
define('LNG', 'pt');                    // Site Language

(Core::isLocal() ? define('ENVDEV', '1') :      // An elegant way of preventing ENVDEV = 1 on deploy
    define('ENVDEV', '0'));                     // Development Enviroment

define('RESTFUL', '1');                         // If attends to ReSTful requests
define('RESTFORMAT', 'json');                   // If ReSTful, which format we're working (JSON please)
define('ENCRYPTURL', '0');                      // If requests must run over encrypted URLs
define('ENCRYPT_POST_DATA', '0');               // If it should encrypt data sent through post
define('METHOD_NOT_FOUND', 'notFound');         // What to call when a method is not found
define('DEFAULT_CONNECTION', 'connection1');    // The default connection used by models


/**
 * The main execution
 */
$core = new core();
$core->execute();
$core->terminate();
