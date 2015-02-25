<?php



Class ExceptionHandler extends Exception {

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


    public static function ExceptionListener() {

        $error = error_get_last();
        if (in_array($error['type'],
            array(E_CORE_ERROR, E_ERROR, E_PARSE, E_COMPILE_ERROR, E_ALL)))
        return self::throwException($error);

    }

    public static function FatalExceptionListener() {

        $error = error_get_last();
        if (in_array($error['type'],
            array(E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE, E_COMPILE_ERROR)))
            return self::throwException($error);

    }

    private static function throwException($error) {

        $trace = debug_backtrace();

        $view = new View();
        $view->setModuleName('krn');
        $view->loadTemplate('exception');

        $view->setVariable('error', $error);
        $view->setVariable('trace', $trace);

        return $view->render(false);

    }

}