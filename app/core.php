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

        $uri = str_replace(BASEDIR,'', $url);
        $uri = explode('/', $uri);

        array_walk($uri, function(&$item){
            strpos($item, '?') == false ||
            $item = substr($item, 0, strpos($item, '?'));
        });

        return $uri;

    }

    /**
     * The constructor
     *
     * It loads the core requirements
     */
    public function __construct() {

        include_once LIBDIR . '/smarty/Smarty.class.php';

        include_once LIBDIR . '/cr.php';
        include_once LIBDIR . '/html.php';
        include_once LIBDIR . '/string.php';

        include_once IFCDIR . '/control.php';
        include_once IFCDIR . '/model.php';
        include_once IFCDIR . '/view.php';

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

        if (!$this->isAjax()) {

            require_once MODDIR . '/home/homeView.php';
            require_once MODDIR . '/home/homeModel.php';
            require_once MODDIR . '/home/homeControl.php';

            $home = new homeControl();
            $home->itStarts($uri);
            exit;
        }

        if (count($uri)>1 && $uri[0] != '' && $uri[1] != '') {
            define('CALL', $uri[0]);
            $module = $uri[0].'Control';
            $action = $uri[1];
            if (method_exists($module,$action)){
                $control = new $module;
                $result = $control->$action();
                echo $result;
                exit;
            }
        }

        exit;
    }

}