<?php

/**
 * Class homeControl
 *
 * This is the Home Controller
 *
 * This controller will handle the first execution.
 * This will automatically identify if the request
 * is running over ajax and will render exactly
 * what's necessary.
 *
 * In other words, if you call an action by URL,
 * this will render the whole site along with the
 * content of the action. But when the home page
 * is already loaded and you call the actions,
 * this will render the action content only and
 * will add/replace where it should.
 *
 * This is also an scaffold for you other modules.
 *
 * @author  Elvis D'Andrea
 * @email   <elvis.vista@gmail.com.br>
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
     * This is where the "magic" happens
     *
     * Always drop a $page_content in your templates
     * so this function can manage to put the content
     * of the action when it's not an ajax request.
     *
     * Prefer a different variable name? Just rename it.
     *
     * @param   array   $uri        - The URI array
     */
    public function itStarts($uri = array()) {

        $this->view()->loadTemplate('home');

        if (count($uri) == 0)
            $uri = array(MAIN);

        $content = Core::getMethodContent($uri);
        $this->view()->setVariable('page_content', $content);
        $this->view()->appendSystemJs();

        /**
         * A few usage examples
         */
        #$this->view->appendJs('example');  // Example on appending module javascript files
        #$this->model()->queryExample(1);   // Example of a query (just remember that the default connection has no data yet)

        #$this->newModel('example');                // Example of how to create a new model connected in a different database
        #$this->model('example')->queryExample();   // This time, the query on queryExample will be executed on the connection of the 'example' file

        #debug($this->view()->getModuleName());     // Example of a code debug

        #throw new ExceptionHandler('teste', 400);  // Example of Exception Handling
        #$this->view('')->lol();                    // Example of Fatal Error Handling


        echo $this->view()->render();

        if (Core::getController())
            echo Core::getController()->view()->injectJSFiles();

        $this->terminate();
    }

    /**
     * When returning the home page, loads the inner content only
     *
     * You can always create a modulePage function that
     * is called when the module is called without an action.
     */
    public function homePage() {

        $this->view()->loadTemplate('overview');
        $this->commitReplace($this->view()->render(), '#main', true);
    }

    /**
     * RESTful GET call example
     */
    public function getHome() {

        #debug($this->view()->getModuleName());     // Example of a code debug

        #throw new ExceptionHandler('teste', 400);  // Example of Exception Handling
        #$this->view('')->lol();                    // Example of Fatal Error Handling

        $result = array(
            'execution'     => 'Hooray, green test!',
            'working_id'    => $this->getId()
        );

        return $result;
    }

    /**
     * When an ajax Method is not found
     *
     * @param   array       $url        - The URL in case you need
     */
    public function notFound($url) {

        $this->view()->setVariable('url', $url);
        $this->commitReplace($this->view()->get404(), 'body');
    }

    /**
     * The view to create a database connection file
     */
    public function createDb() {

        $this->view()->loadTemplate('createdb');
        $this->commitReplace($this->view()->render(), '#main');
    }

    /**
     * The action to save a database connection file
     */
    public function saveDbFile() {

        $this->validatePost('conname', 'host', 'user', 'pass', 'db') ||
            $this->commitReplace('Please fill all information.', '#alert', false);

        $this->model()->generateConnectionFile(
            $this->getPost('conname'),
            $this->getPost('host'),
            $this->getPost('user'),
            $this->getPost('pass'),
            $this->getPost('db')
        );
        $this->commitReplace('Created!', '#alert');
    }

}