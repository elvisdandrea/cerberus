<?php

/**
 * Class Rest
 *
 * The ReSTful interpretation of methods
 * to easily handle requests
 *
 * (it's under tests the advantage of
 * handling it using static functions)
 */
class RestServer {

    /**
     * The list of valid types
     *
     * @var array
     */
    private static $validMimeTypes = array(
        'json'  => 'application/json',
        'xml'   => 'application/xml'
    );

    /**
     * Authenticate?
     *
     * A centered function for
     * token based authentication
     */
    public static function authenticate() {

        $authControl = new authControl();
        $authControl->authenticate();
        unset($authControl);
    }

    /**
     * Executes The RESTful Method
     *
     * @param  array    $uri        - The method URI
     * @throws ExceptionHandler
     */
    public static function runRestMethod(array $uri) {

        self::setFormat(RESTFORMAT, false);
        self::authenticate();
        if (count($uri) < 1 || $uri[0] == '')
            throw new ExceptionHandler(Language::REST_NO_METHOD(), 400);

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!isset($uri[1]) || $uri[1] == '' || intval($uri[1]) > 0) $action = $request_method . $uri[0];
        else $action = $request_method . $uri[1];

        $module = $uri[0].'Control';

        if (!method_exists($module, $action) || !is_callable(array($module, $action)))
            throw new ExceptionHandler(Language::METHOD_NOT_FOUND(intval($uri[1]) > 0 ? $uri[0] : $uri[1], $request_method), 404);

        $control = new $module;
        !intval($uri[1]) > 0 || $control->setId($uri[1]);

        $result = $control->$action();
        self::response($result);
        unset($control);
        self::terminate();
    }

    /**
     * Sets a response header
     *
     * @param   string      $header     - The header name
     * @param   string      $value      - The header value
     */
    public static function setHeader($header, $value) {

        header($header . ': ' . $value);
    }

    /**
     * Sets the content type format and acceptable formats
     *
     * @param   string              $format             - The format (so far: json | xml )
     * @param   bool                $acceptFormatOnly   - If it must add Accept Header
     * @throws  ExceptionHandler
     */
    public static function setFormat($format, $acceptFormatOnly = false) {

        if (!isset(self::$validMimeTypes[$format]))
            throw new ExceptionHandler('The server has an invalid configuration (invalid mime for ' . $format . ')', 502);

        self::setHeader('Content-type', self::$validMimeTypes[$format]);
        $acceptFormatOnly || self::acceptableHeaders(array(self::$validMimeTypes[$format]));

    }

    /**
     * Sets the response code
     *
     * @param   int     $code       - The response HTTP code
     */
    public static function setResponseCode($code) {

        http_response_code($code);
    }

    /**
     * Adds Accept headers
     *
     * @param   array   $acceptables    - An array with the list of acceptable mime types
     */
    public static function acceptableHeaders(array $acceptables) {

        $accepHeaders = explode(',', Core::getHttpHeaders('Accept'));

        foreach ($acceptables as $acceptable)
            in_array($acceptable, $accepHeaders) || self::throwError(Language::NOT_ACCEPTABLE($acceptable), 406);

    }

    /**
     * Throws a 404 Error
     */
    public static function throw404() {

        throw new ExceptionHandler('Resource not found', 404);
    }

    /**
     * ReSTful error throw
     *
     * In case a catchable error or validation error,
     * throwing a json (or desired ReST format) with status 400 is a good concept
     *
     * @param   string      $message        - A mensagem de texto
     * @param   int         $status         - THe error status
     * @return  array
     * @throws  ExceptionHandler
     */
    public static function throwError($message, $status = 400) {

        if(RESTFUL == '1')
            throw new ExceptionHandler($message, $status);

        $response = array(
            'status'        => $status,
            'message'       => $message
        );

        return $response;

    }

    /**
     * ReSTful Response
     *
     * @param   array   $data       - Array com os dados da resposta
     * @return  string
     * @throws  Exception
     */
    public static function response(array $data, $statusCode = 200) {

        if(RESTFUL == '1') {
            $response = json_encode($data, JSON_UNESCAPED_UNICODE);
            self::setResponseCode($statusCode);
            echo $response;
            self::terminate();
        }

        return $data;
    }

    /**
     * ReSTful Termination Process
     *
     */
    public static function terminate() {
        //TODO: termination process
        exit;
    }

}