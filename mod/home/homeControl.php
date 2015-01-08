<?

/**
 * Class homeControl
 *
 */


class homeControl extends Control {

    /**
     * The View object
     *
     * @var     homeView
     */
    private $view;

    /**
     * The constructor
     *
     * It instances the view object
     * We have no need for Model for this class
     */
    public function __construct() {
        $this->view = new homeView();
    }

    /**
     * Where it all starts
     *
     * For the first run, this is
     * the function that will render
     * the whole page with no ajax
     */
    public function itStarts($uri = array()) {

        $this->view->loadTemplate('index');

        if (count($uri)>1 && $uri[0] != '' && $uri[1] != '') {
            define('CALL', $uri[0]);
            $module = $uri[0].'Control';
            $action = $uri[1];

            if (method_exists($module, $action)) {
                $control = new $module;
                ob_start();
                $control->$action();
                $result = ob_get_contents();
                ob_end_clean();
                $this->view->setVariable('page_content', $result);
            }

        }
        echo $this->view->render();
        exit;
    }

    /**
     * When returning the home page, loads the inner content only
     */
    public function index() {
        #$this->view->loadTemplate( 'elements_example');        //load this template to see the theme elements
        $this->view->loadTemplate( LNG . '/centercontent');
        $this->commitReplace($this->view->render(), '#two');
    }
}