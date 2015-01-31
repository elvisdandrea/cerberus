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

        if (!method_exists($module, $action) || !is_callable(array($module, $action))) {
            if (!self::isAjax()) return;

            $notFoundAction = METHOD_NOT_FOUND;
            $home = self::requireHome();
            $home->$notFoundAction($uri);
            self::terminate();
        }

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
     * Include Home Page Module Classes
     */
    private static function requireHome() {

        foreach (array('Model', 'Control') as $pos => $class)
            require_once MODDIR . '/' . HOME . '/' .  HOME  . $class . '.php';
        $homeClass = HOME . 'Control';
        return new $homeClass();
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

        if (RESTFUL == '1')
            RestServer::authenticate();

        /**
         * Going Home
         */
        if (!$this->isAjax()) {

            $home = $this->requireHome();
            $home->itStarts($uri);
            $this->terminate();
        }

        $this->runMethod($uri);
        $this->terminate();
    }

    /**
     * Prevent Memory Leaks
     */
    public function terminate() {

        unset($this);
        exit;
    }

}