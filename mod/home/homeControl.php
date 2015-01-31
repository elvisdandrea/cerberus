<?php

/**
 * Class homeControl
 *
 */


class homeControl extends Control {

    /**
     * The constructor
     *
     * The parent constructor is the
     * base for the controller functionality
     *
     * This will automatically handle the instantiation
     * of the module Model and View
     */
    public function __construct() {
        parent::__construct();
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
                $result = $this->view()->get404();

            $this->view()->setVariable('page_content', $result);
        }

        $this->view()->loadTemplate('home');

        /**
         * A few use examples
         */
        #$this->view->appendJs('example');  // Example on appending module javascript files
        #$this->model()->queryExample(1);   // Example of a query (just remember that the default connection has no data yet)

        #$this->newModel('example');                // Example of how to create a new model connected in a different database
        #$this->model('example')->queryExample();   // This time, the query on queryExample will be executed on the connection of the 'example' file

        echo $this->view()->render();
        $this->terminate();
    }

    /**
     * When returning the home page, loads the inner content only
     */
    public function homePage() {

        $this->view()->loadTemplate('overview');
        $this->commitReplace($this->view()->render(), '#main', true);
    }

    /**
     * When an ajax Method is not found
     *
     * @param   array       $url        - The URL in case you need
     */
    public function notFound($url) {

        $this->commitReplace($this->view()->get404(), 'body');
    }

    /**
     * The view to create a database file
     */
    public function createDb() {
        $this->view()->loadTemplate('createdb');
        $this->commitReplace($this->view()->render(), '#main');
    }

    /**
     * The action to save a database file
     */
    public function saveDbFile() {

        $post = $this->getPost();
        RestServer::validate($post, array('conname', 'host', 'user', 'pass', 'db'));
        $this->model()->generateConnectionFile($post['conname'], $post['host'], $post['user'], $post['pass'], $post['db']);
        $this->commitAdd('Created!', '#alert');
    }
}