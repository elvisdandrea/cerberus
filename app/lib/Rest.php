<?php

/**
 * Class Rest
 *
 * The ReSTful interpretation of methods
 * to easily handle requests
 */
class Rest {

    /**
     * Authenticate?
     *
     * A centered function for
     * token based authentication
     */
    public static function authenticate() {

        //TODO: ReSTful Authentication method
    }

    /**
     * Validate Required Values in a array
     *
     * @param   array   $data           - The data array
     * @param   array   $validation     - An array with a list of indexes that must contain in the data array
     */
    public static function validate($data, $validation = array()) {

        foreach ($validation as $index)
            (isset($data[$index]) && $data[$index] != '') || self::throwError('The value "' . $index . '" is required for this method.');
    }

    /**
     * Throws a 404 Error
     *
     * Used for security features
     */
    public static function throw404() {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    /**
     * ReSTful error throw
     *
     * In case a catchable error or validation error,
     * throwing a json (or desired ReST format) with status 400 is a good concept
     *
     * @param   string      $message        - A mensagem de texto
     * @throws  Exception
     */
    public static function throwError($message) {

        //TODO: handle other formats
        http_response_code(400);
        header('Content-type: application/json');

        $response = array(
            'status'        => 400,
            'message'       => $message
        );

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ReSTful Response
     *
     * @param   array   $data       - Array com os dados da resposta
     * @throws  Exception
     */
    public static function response(array $data) {

        //TODO: handle other formats
        http_response_code(200);
        header('Content-type: application/json');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

}