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
     * The home page
     *
     * @param   array   $uri        - The URI array
     */
    public function itStarts($uri = array()) {

        $this->view->loadTemplate('index');

        if (count($uri) > 0) {
            ob_start();
            Core::runMethod($uri);
            $result = ob_get_contents();
            ob_end_clean();
            if ($result != '')
                $this->view->setVariable('page_content', $result);
        }

        echo $this->view->render();
        exit;
    }

    /**
     * When returning the home page, loads the inner content only
     */
    public function homePage() {
        #$this->view->loadTemplate( 'elements_example');        //load this template to see the theme elements
        $this->view->loadTemplate( LNG . '/centercontent');
        $this->commitReplace($this->view->render(), '#two', true);
    }
}