<?php
/**
 * App Handler File
 *
 * These functions will be called automatically
 * when an specific event occurs.
 * If a class is not defined and is called,
 * the handler will automatically include the
 * file containing the class name
 *
 * @author:  Elvis D'Andrea
 * @email:  elvis.vista@gmail.com
 */

/**
 * Error Message Level
 *
 * Removing E_ALL is not recommended
 */

error_reporting(E_ERROR | E_PARSE | E_ALL);

/**
 * Handler functions registration
 */
require_once KRNDIR . '/AutoloadHandler.php';
spl_autoload_register(array('AutoloadHandler', 'autoLoad'));
set_error_handler(array('ExceptionHandler','ExceptionListener'));
set_exception_handler(array('ExceptionHandler','ExceptionListener'));
register_shutdown_function(array('ExceptionHandler','FatalExceptionListener'));
session_start();


/**
 * Debugger Function
 *
 * To support a debug in any position of the code,
 * regardless the possibility of a template engine,
 * this must be text-based
 *
 * @param $mixed
 * @param $element
 */
function debug($mixed, $element = 'html'){

    //TODO: Use Termination function
    echo Debugger::debug($mixed, $element);
    exit;
}