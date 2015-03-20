<?php

/**
 * Class core   - The System Core
 *
 * This class handles the active request
 *
 * The Core is the framework router as well.
 * There is no need to create any configuration file
 * to handle the url routing, this will identify
 * the module and action in the url and will
 * automatically instance the necessary objects and
 * call the action.
 *
 * There's also no need for hard-coded function names with damn
 * WTFAction, the action may have any name.
 *
 * What if I need some function in the controller that
 * should not be callable from url? Make a non-callable
 * private function, so you can use in the controller,
 * the core class automatically identifies it.
 *
 * So don't struggle! To create your module is just simple as:
 *
 * - Create a folder with your module name in /mod
 * - Create a class named yourModuleControl that extends Control and is named yourClassControl.php
 * - Create a class named yourModuleModel that extends Model and is named yourClassModel.php
 * - Create a constructor in your classes that calls parent::__construct(); (you may add code in the constructor if you need)
 * - Profit! It's ready! Just go coding!
 *
 * After that, for every public function you create in your controller you will have your action.
 * The URL will be: http://yoursite.com/yourmodulename/yourfunctionname
 *
 * The URL is handled automatically. The content is handled automatically when it's not called via ajax.
 * There will be no memory leak. The core does strict the necessary, straight forward to what's important.
 * So there will be no shit load of "cache" php class with a lot of shit that goes raping the server like there's no tomorrow.
 * Security, anti-injection, anti-header-overriding protection, everything is automatic.
 * Modules, Libs and any class are loaded only if necessary, and you don't have to include/require anything.
 * Error handling is automatic. You may create your throw events without worrying about coding an exception handler.
 *
 * @author  Elvis D'Anddrea
 * @email   <elvis.vista@gmail.com>
 */

class core {

    /**
     * The current working controller object
     *
     * @var
     */
    private $controller;

    /**
     * Static controllers
     *
     * @var
     */
    private static $static_controller;

    /**
     * Request Headers
     *
     * @var array
     */
    private static $headers = array();

    /**
     * Server Data
     *
     * @var array
     */
    private static $server = array();

    /**
     * The Remote Address
     *
     * @var
     */
    private static $remote_address;

    /**
     * The URL Loader
     *
     * Thou shalt not call superglobals directly
     *
     * @return  array|mixed
     */
    private static function loadUrl(){

        $uri = $_SERVER['REQUEST_URI'];

        if (ENCRYPTURL == '1')
            $uri = CR::decrypt($uri);

        BASEDIR == '/' || $uri = str_replace(BASEDIR,'', $uri);
        $uri = explode('/', $uri);

        array_walk($uri, function(&$item){
            strpos($item, '?') == false ||
            $item = substr($item, 0, strpos($item, '?'));
        });

        return $uri;
    }

    /**
     * Returns the working domain
     */
    public static function getDomain() {

        return filter_input(INPUT_SERVER, 'SERVER_NAME');
    }

    /**
     * Checks if server is under a certain subdomain
     *
     * @param   string      $subdomain      - The subdomain to test
     * @return  bool
     */
    public static function isUnderSubdomain($subdomain) {

        $domain = explode('.' ,self::getDomain());
        return $domain[0] == $subdomain;
    }

    /**
     * Gets server values
     */
    private static function parsetServerData() {

        self::$server = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_ENCODED);
    }

    /**
     * Parses HTTP headers
     *
     * @return  array
     */
    private static function parseRequestHeaders() {
        $headers = array();
        foreach(self::$server as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') continue;
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        self::$headers = $headers;
        self::$remote_address = self::$server['REMOTE_ADDR'];
    }

    /**
     * Returns HTTP Headers
     *
     * @param   bool            $header
     * @return  array|string
     */
    public static function getHttpHeaders($header = false) {
        if ($header)
            return isset(self::$headers[$header]) ? self::$headers[$header] : '';

        return self::$headers;
    }

    /**
     * Returns the user remote address
     *
     * @return mixed
     */
    public static function getRemoteAddress() {

        return self::$remote_address;
    }

    /**
     * Tests if user is logged in
     *
     * @return bool
     */
    public static function isLoggedIn() {

        $uid = Session::get('uid');
        return is_array($uid) && isset($uid['db_connection']);
    }

    /**
     * Returns the called URI
     *
     * @return array|mixed
     */
    public static function getURI() {

        return self::loadUrl();
    }

    /**
     * Executes the Method called by URI
     *
     * If no module is called, then let's go home
     * If the module is called as a folder, then
     * it searches for a modulePage function that
     * is supposed to be the hotpage of the module.
     *
     * @param   array       $uri        - The method class and method
     */
    public static function runMethod($uri) {

        if (count($uri) < 1 || $uri[0] == '') return;

        $module = $uri[0].'Control';
        if (!isset($uri[1]) || $uri[1] == '') $uri[1] = $uri[0] . 'Page';

        $action = $uri[1];

        if (!method_exists($module, $action) || !is_callable(array($module, $action))) {
            if (!self::isAjax()) return;

            $notFoundAction = METHOD_NOT_FOUND;
            self::$static_controller = self::requireHome();
            if (self::isLoggedIn())
                self::$static_controller->newModel('uid');

            self::$static_controller->$notFoundAction($uri);
            self::terminate();
        }

        self::$static_controller = new $module;
        $result = self::$static_controller->$action();
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
        return isset(self::$server['HTTP_X_REQUESTED_WITH']) && strtolower(self::$server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Is it running localhost server or prod server?
     *
     * @return bool
     */
    public static function isLocal() {
        return (strpos(filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_ENCODED), '192.168') !== false ||
            filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_ENCODED) == 'localhost');
    }

    /**
     * Include Home Page Module Classes
     */
    private static function requireHome() {

        foreach (array('Model', 'Control') as $class)
            require_once MODDIR . '/' . HOME . '/' .  HOME  . $class . '.php';
        $homeClass = HOME . 'Control';
        return new $homeClass();
    }

    /**
     * Verifies if user is authenticated.
     * If not, redirect to login page
     *
     * @param   array       $uri        - The working uri
     */
    private function checkAuthenticated(array $uri) {

        if (self::isLoggedIn()) return;
        // TODO: Refractor me, for the lord's sake
        if ($this->isAjax()) {
            if (count($uri) == 0 || !($uri[0] == 'auth' && $uri[1] == 'login')) {
                Html::refresh();
                $this->terminate();
            }
        } else {
            $authControl = new authControl();
            echo $authControl->loginPage();
            $this->terminate();
        }
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

        $this->parsetServerData();
        $this->parseRequestHeaders();

        $uri = $this->loadUrl();                    // Loads the called URL
        String::arrayTrimNumericIndexed($uri);      // Trim the URL array indexes

        /**
         * When server is running as a RESTful server
         */
        if (RESTFUL == '1')  {
            RestServer::runRestMethod($uri);
            $this->terminate();
        }

        /**
         * When it's required to be a logged user to access
         */
        if (REQUIRE_LOGIN == '1')
            $this->checkAuthenticated($uri);

        /**
         * When the request is not running over ajax,
         * then call the home for full page rendering
         * before calling the requested method
         */
        if (!$this->isAjax()) {

            $this->controller = $this->requireHome();
            if (self::isLoggedIn())
                $this->controller->newModel('uid');

            $this->controller->itStarts($uri);
            $this->terminate();
        }

        /**
         * Normal Ajax Request, call the method only
         */
        $this->runMethod($uri);
        $this->terminate();
    }

    /**
     * Prevent Memory Leaks of the core class
     */
    public function terminate() {

        unset($this->controller);
        unset($this);
        exit;
    }

}