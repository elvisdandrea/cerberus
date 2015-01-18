<?php

/**
 * Class core
 *
 * This class handles the active request
 *
 */
class core {

    /**
     * The URL Loader
     *
     * Thou shalt not call superglobals directly
     *
     * @return  array|mixed
     */
    private function loadUrl(){

        $url = $_SERVER['REQUEST_URI'];

        if (ENCRYPTURL == '1')
            $url = CR::decrypt($url);

        $uri = str_replace(BASEDIR,'', $url);
        $uri = explode('/', $uri);

        array_walk($uri, function(&$item){
            strpos($item, '?') == false ||
            $item = substr($item, 0, strpos($item, '?'));
        });

        return $uri;
    }

    /**
     * Executes the Method called by URI
     *
     * @param   array       $uri        - The method class and method
     */
    public static function runMethod($uri) {

        if (count($uri) < 1 || $uri[0] == '') return;

        define('CALL', $uri[0]);
        $module = $uri[0].'Control';
        if ($uri[1] == '') $uri[1] = $uri[0] . 'Page';

        $action = $uri[1];

        if (!method_exists($module, $action)) return;

        $control = new $module;
        $result = $control->$action();
        echo $result;
    }

    /**
     * The constructor
     *
     * It loads the core requirements
     */
    public function __construct() {

        foreach(array(
                    LIBDIR . '/smarty/Smarty.class.php',

                    LIBDIR . '/cr.php',
                    LIBDIR . '/html.php',
                    LIBDIR . '/string.php',

                    IFCDIR . '/control.php',
                    IFCDIR . '/model.php',
                    IFCDIR . '/view.php')

                as $dep) include_once $dep;

    }

    /**
     * Is the request running over ajax?
     *
     * @return bool
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Validar dados obrigatórios em um array
     *
     * @param   array   $data           - O array a ser verificado
     * @param   array   $validation     - Um array contendo a lista de índices que $data deve conter
     */
    public static function validate($data, $validation = array()) {

        foreach ($validation as $index)
            (isset($data[$index]) && $data[$index] != '') || self::throwError('Voce deve informar "' . $index . '" para este metodo.');
    }

    /**
     * Autenticar?
     *
     * Um processo simples, sem token, apenas uma
     * pequena garantia de que não será feito force
     */
    public static function authenticate() {

        //TODO: ReSTful Authentication method
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

    /**
     * The main execution
     *
     * It will verify the URL and
     * call the module and action
     *
     * When the call is not Ajax, then
     * there's no place like home
     */
    public function execute() {

        $uri = $this->loadUrl();
        String::arrayTrimNumericIndexed($uri);

        /**
         * Going Home
         */
        if (!$this->isAjax()) {

            foreach (array('View', 'Model', 'Control') as $class)
                require_once MODDIR . '/' . HOME . '/' . HOME . $class . '.php';

            $homeClass = HOME . 'Control';
            $home = new $homeClass();
            $home->itStarts($uri);
            exit;
        }

        $this->runMethod($uri);
        exit;
    }

}