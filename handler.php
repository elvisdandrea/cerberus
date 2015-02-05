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
 * Remove E_ALL flag if you are a lazy fuck
 */
error_reporting(E_ERROR | E_PARSE | E_ALL);

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
    <html>
        <head>
            <title>Cerberus - Do it simple and do it efficiently</title>
        </head>
    <body>
    <style>
        html {
            clear: both;
            background: url("<?php echo IMGURL . '/bg.jpg'; ?>") repeat scroll 0 0 rgba(0, 0, 0, 0);
            font-family: "Strait",sans-serif;
        }

        h1 {
            clear: both;
            color: #fff;
            padding: 30px;
            font-family: "Fjalla One",sans-serif;
            font-size: 25px;
            margin-top: 1px;
            text-shadow: 6px 1px 6px #333;
        }
        label {
            clear: both;
            border: medium none;
            color: #98af95;
            font-family: "Strait",sans-serif;
            font-size: 18px;
            outline: medium none;
            padding: 6px 30px 6px 6px;
            margin: 0;
            display: block;
        }
        .banner {
            margin: 100px auto 0;
            width: 50%;
        }
        .message {
            background: none repeat scroll 0px 0px rgba(0, 0, 0, 0.25);
            text-shadow: 6px 1px 6px #333;
            padding: 1.2em;
        }
    </style>
        <div class="banner">
            <h1>
                <img src="<?php echo IMGURL . '/logo.png'; ?>" alt="cerberus_logo" width="90px"/>
                Sorry, something went bad!</h1>
            <div class="message">
                <label>I know this is emarassing, but the server must be under maintenance. Please come back later.</label>
                <?php if (ENVDEV == '0') exit; ?>
                <label>
                    Error: <?php echo $error['type']; ?> <br>
                    Message: <?php echo $error['message']; ?> <br>
                    File: <?php echo $error['file']; ?> <br>
                    Line: <?php echo $error['line']; ?>
                </label>
            </div>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Debugger Function
 *
 * To support a debug in any position of the code,
 * regardless the possibility of a template engine,
 * this must be text-based
 *
 * @param $mixed
 */
function debug($mixed){
    $call = debug_backtrace();
    ob_start();

    if (isAjax()) : ?>
        Html.Replace('
    <?php endif; ?>
    <html>
    <head>
        <title>Cerberus - Do it simple and do it efficiently</title>
    </head>
    <body>
    <style>
        html {
            clear: both;
            background: url("<?php echo IMGURL . '/bg.jpg'; ?>") repeat scroll 0 0 rgba(0, 0, 0, 0);
            font-family: "Strait",sans-serif;
        }

        h1 {
            clear: both;
            color: #fff;
            padding: 30px;
            font-family: "Fjalla One",sans-serif;
            font-size: 25px;
            margin-top: 1px;
            text-shadow: 6px 1px 6px #333;
        }
        label {
            clear: both;
            border: medium none;
            color: #98af95;
            font-family: "Strait",sans-serif;
            font-size: 18px;
            outline: medium none;
            padding: 6px 30px 6px 6px;
            margin: 0;
            display: block;
        }
        .banner {
            margin: 100px auto 0;
            width: 50%;
        }
        .message {
            background: none repeat scroll 0px 0px rgba(0, 0, 0, 0.25);
            text-shadow: 6px 1px 6px #333;
            padding: 1.2em;
        }
    </style>
    <div class="banner">
        <h1>
            <img src="<?php echo IMGURL . '/logo.png'; ?>" alt="cerberus_logo" width="90px"/>
            Cerberus - Debugging Code</h1>
        <div class="message">
            <label>
                <pre>
                <?php
                    print_r($mixed);
                ?>
                    </pre>
            </label>
            <label>
                <hr>
                <label>Debug Trace:</label>
                <?php foreach ($call as $action) : ?>
                    <hr>
                <ul>
                    <li>File: <?php echo $action['file']; ?></li>
                    <li>Line: <?php echo $action['line']; ?></li>
                    <li>Class: <?php echo $action['class']; ?></li>
                    <li>Function: <?php echo $action['function']; ?></li>
                </ul>
                <?php endforeach; ?>
            </label>
        </div>
    </div>
    </body>
    </html>
    <?php if (isAjax()) : ?>
        ', 'html');
    <?php endif;
    $result = ob_get_contents();
    ob_clean();
    echo preg_replace( '/\s+/', ' ', trim( $result ) );
    exit;
}