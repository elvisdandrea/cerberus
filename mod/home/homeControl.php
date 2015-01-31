<?php

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

        if (count($uri) > 0) {
            ob_start();
            Core::runMethod($uri);
            $result = ob_get_contents();
            ob_end_clean();
            if ($result == '')
                $result = $this->view->get404();

            $this->view->setVariable('page_content', $result);
        }

        $this->view->loadTemplate('home');

        #$this->view->appendJs('example');  // Example on appending module javascript files

        echo $this->view->render();
        $this->terminate();
    }

    /**
     * When returning the home page, loads the inner content only
     */
    public function homePage() {

        $this->view->loadTemplate('overview');
        $this->commitReplace($this->view->render(), '#center', true);
    }

    /**
     * When an ajax Method is not found
     *
     * @param   array       $url        - The URL in case you need
     */
    public function notFound($url) {

        $this->commitReplace($this->view->get404(), 'body');
    }
}