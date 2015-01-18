<?php
/**
 * App Handler File
 *
 * These functions will be called automatically
 * when an error event occurs.
 * If a class is not defined and is called,
 * the handler will automatically include the
 * file containing the class name
 *
 * @author:  Elvis D'Andrea
 * @email:  elvis.vista@gmail.com
 */

/**
 * Error Message Level
 */
error_reporting(E_ERROR | E_PARSE);

/**
 * Handler functions registration
 */
register_shutdown_function('fatalErrorHandler');
spl_autoload_register('autoLoad');

/**
 * Is it an ajax request?
 *
 * A function outside core to handle
 * when even core isn't working
 *
 * @return  bool
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Is it running localhost server or prod server?
 *
 * @return bool
 */
function isLocal() {
    return (strpos($_SERVER['SERVER_ADDR'], '192.168') !== false || $_SERVER['HTTP_HOST'] == 'localhost');
}


/**
 * Fatal Error Handler
 *
 * To support all kind of errors, this must
 * be text-based instead of loading template files
 */
function fatalErrorHandler(){
    $error = error_get_last();
    if (!in_array($error['type'],
        array(
            E_ERROR,
            E_USER_ERROR,
            E_PARSE,
            E_COMPILE_ERROR
        ))
    ) return; ?>
    <h2>Sorry, something went bad!</h2>
    I know this is emarassing, but the server must be under maintenance. Please come back later.
    <?php if (ENVDEV == '0') exit; ?>
    <br><br>
    Error: <?php echo $error['type']; ?> <br>
    Message: <?php echo $error['message']; ?> <br>
    File: <?php echo $error['file']; ?> <br>
    Line: <?php echo $error['line']; ?>
    <?php exit;
}

/**
 * Class Autoload Handler
 *
 * @param $class_name
 */
function autoLoad($class_name) {

    $search = array(
        MODDIR . '/' . CALL,
        LIBDIR
    );

    foreach ($search as $dir) {
        $file = $dir . '/' . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}