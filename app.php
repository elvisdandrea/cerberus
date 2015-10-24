<?php

/**
 * App Configuration and Bootstrap File
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
 * In addition, it's a backend-based SPA framework, and it will do it automatically.
 * Add the attribute "changeurl" to the links you need to follow the URL and suppress
 * on those you want to be an internal function.
 *
 * The system will render the page automatically when an internal URL is called in browser,
 * you don't have to add code to do it.
 *
 * @author: Elvis D'Andrea
 * @email:  <elvis.vista@gmail.com>
 */

/**
 * Directory Definition
 */
define('SERVER_NAME', filter_input(INPUT_SERVER, 'SERVER_NAME'));
define('PHP_SELF',    filter_input(INPUT_SERVER, 'PHP_SELF'));
define('MAINURL',   (strpos(SERVER_NAME, 'http://') !== false ? SERVER_NAME : 'http://' . SERVER_NAME ) . dirname(PHP_SELF) );
define('BASEDIR',   (dirname(PHP_SELF) != '/' ? dirname(PHP_SELF) . '/' : '/'));

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
define('RESTFUL', '0');                         // If attends to ReSTful requests

define('HOME', 'home');                         // Home Sweet Home - The module name to be used as home module
define('MAIN', 'home');                         // The well known Main() - the bootstrap function after core loading

define('AUTH', 'auth');                         // The module to be used as authentication module
define('TEMPLATE', 'default');                  // The default views template
define('APPTITLE', ' - The fastest backend SPA'); // Define the static portion of page title here

define('REQUIRE_LOGIN', '0');                   // If this framework should require a logged user
define('LOGIN_UID',   'uid');                   // The register position of the login uid

define('LNG', 'pt');                            // Site Language

(Core::isLocal() ?                              // An elegant way of preventing ENVDEV = 1 on deploy server
    define('ENVDEV', '1') :                     // Development Enviroment is ON
    define('ENVDEV', '0'));                     // Development Enviroment is OFF

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
