<?php

/**
 * Class ExceptionHandler
 *
 * The class for handling exceptions
 *
 *
 *
 */

Class ExceptionHandler extends Exception {

    /**
     * The constructor
     *
     * This is the user exception handler function
     *
     * @param   string    $message      - The error message
     * @param   int       $status       - The status code
     */
    public function __construct($message, $status = 400) {

        http_response_code($status);
        ini_set('display_errors', '0');
        $error = array(
            'message'   => $message,
            'status'    => $this->getCode(),
            'file'      => $this->getFile(),
            'line'      => $this->getLine()
        );
        $this->throwException($error);
        return parent::__construct($message, $status);

    }


    /**
     * The exception listener
     *
     * This function must be set as
     * the default error handler
     *
     * @return  string      - the thrown error
     */
    public static function ExceptionListener() {

        $error = error_get_last();
        if (in_array($error['type'],
            array(E_CORE_ERROR, E_ERROR, E_PARSE, E_COMPILE_ERROR, E_ALL)))
        return self::throwException($error);

    }

    /**
     * Fatal Exception Listener
     *
     * This function handles fatal exceptions
     * and must be set as the shutdown function
     *
     * @return  string      - The thrown error
     */
    public static function FatalExceptionListener() {

        $error = error_get_last();
        if (in_array($error['type'],
            array(E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE, E_COMPILE_ERROR)))
            return self::throwException($error);

    }

    /**
     * Returns the error page
     *
     * The page is rendered using the
     * view template handler
     *
     * It also includes the trace of
     * the current execution
     *
     * This page can be edited in
     * the file tpl/krn/exception.tpl
     *
     * @param   $error      - The error trace ( an array('message' => 'The Error Message', 'file' => 'The File Name', 'Class' => 'The Class Name') )
     * @return  string      - The rendered error page
     */
    private static function throwException(array $error) {

        $trace = debug_backtrace();

        $view = new View();
        $view->setModuleName('krn');
        $view->loadTemplate('exception');

        $view->setVariable('error', $error);
        $view->setVariable('trace', $trace);

        return $view->render(false);

    }

}