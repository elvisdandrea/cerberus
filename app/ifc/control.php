<?php

/**
 * Class Control
 *
 * The App Controller Class
 *
 * All you need to handle the request in the fundamental level.
 * Every other super stuffs are libs and you can use as you need.
 *
 * This class will automatically instances Models and Views of
 * you module by using reflection. In other words, you don't need
 * to create properties in the module controller class for models
 * and views.
 *
 * You can have as many models as you need, each of them using a different
 * connection if necessary.
 *
 * You can have as many views as you need and render them in a specific
 * element. This guarantees a small javascript file with only the necessary.
 *
 * If this sounds blasphemy to you, remember that we want this to run
 * faster than the blink of an eye, so stop this bullshit of running everything on
 * the frontend, specially render HTML on frontend, because this requires double
 * processing (one on backend and other on frontend).
 *
 * We want to deliver the smallest of things, only the necessary,
 * and something that will be parsed only once by browser.
 * Also, the smaller the javascript, the faster the browser processes it.
 *
 * Keep in mind: every bit matters!
 *
 * @author  Elvis D'Andrea
 * @email   <elvis.vista@gmail.com>
 */

class Control {

    /**
     * When in RESTful server, the id
     * being manipulated
     *
     * @var
     */
    private $id = 0;

    /**
     * The authenticated user data
     *
     * @var array
     */
    private $uid = array(
        'token'         => '',
        'refresh_token' => '',
        'id'            => ''
    );

    /**
     * Thou shalt not call superglobals directly
     *
     * @var
     */
    private $post;


    /**
     * Thou shalt not call superglobals directly
     *
     * @var
     */
    private $get;

    /**
     * The Object View
     *
     * @var View
     */
    private $view = array();

    /**
     * The Object Model List
     *
     * @var
     */
    private $model = array();

    /**
     * The Controller Module Name
     *
     * @var
     */
    private $moduleName;

    /**
     * Thou shalt not call superglobals directly
     * even though I'm doing it in this function
     */
    public function __construct() {
        $this->post = String::ClearArray($_POST);
        $this->get  = String::ClearArray($_GET);

        $ref = new ReflectionClass($this);
        $this->moduleName = basename(dirname($ref->getFileName()));

        $this->newView();
        $this->newModel();
    }

    /**
     * Returns the desired View
     *
     * @param   string      $name       - The View name
     * @return  View
     */
    public function view($name = 'default') {

        if (!isset($this->view[$name])) return false;
        return $this->view[$name];
    }

    /**
     * Creates a new View in the view list
     *
     * @param   string      $name       - The View name
     * @return  bool
     */
    public function newView($name = 'default') {

        $this->view[$name] = new View();
        $this->view($name)->setModuleName($this->moduleName);
    }

    /**
     * Returns the desired model
     *
     * @param   string      $name       - The model name
     * @return  bool
     */
    public function model($name = DEFAULT_CONNECTION) {

        if (!isset($this->model[$name])) return false;
        return $this->model[$name];
    }

    /**
     * Creates a new Model in the Model list
     *
     * Specify no name to reset default model
     *
     * @param   string      $name       - The model and connection name
     */
    public function newModel($name = DEFAULT_CONNECTION) {

        $model = $this->moduleName . 'Model';
        $this->model[$name] = new $model($name);
    }

    /**
     * Returns a post value
     *
     * @return  mixed
     */
    protected function getPost() {

        $args = func_get_args();

        if (count($args) == 0)
            return $this->post;

        if (count($args) == 1)
            return $this->post[$args[0]];

        $result = array();
        foreach ($args as $arg)
            !isset($this->post[$arg]) || $result[$arg] = $this->post[$arg];

        return $result;
    }

    /**
     * Sets the Working ID
     *
     * @param $id
     */
    public function setId($id) {

        $this->id = $id;
    }

    /**
     * Returns the current working ID
     *
     * @return int
     */
    public function getId() {

        return $this->id;
    }

    /**
     * Sets the current UID
     *
     * @param   string  $token              - The current working token
     * @param   string  $refresh_token      - The token for refreshing authentication
     * @param   int     $id                 - The current user ID
     */
    public function setUID($token, $refresh_token, $id) {
        $this->uid = array(
            'token'             => $token,
            'refresh_token'     => $refresh_token,
            'id'                => $id
        );
    }

    /**
     * Validates if a POST value is empty
     *
     * Indexes should be passed as
     * parameter
     *
     * @return bool
     */
    protected function validatePost() {

        $args = func_get_args();

        foreach ($args as $arg)
            if (!isset($this->post[$arg]) || $this->post[$arg] == '')
                return false;

        return true;
    }

    /**
     * Throws a 404 Error
     *
     * Used for security features
     */
    protected function throw404() {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    /**
     * Returns a URI query string value
     *
     * @param   bool|string     $name       - the query string field name
     * @return  mixed
     */
    protected function getQueryString($name = false) {
        if ($name)
            return (isset($this->get[$name]) ? $this->get[$name] : false);

        return $this->get;
    }

    /**
     * Renders a HTML onto screen
     *
     * Still to be implemented
     *
     * @param $html
     */
    protected function commitPrint($html) {
        echo $html;
        $this->terminate();
    }

    /**
     * Renders a HTML replacing
     * the content of a element
     *
     * @param   string      $html   - The HTML content
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitReplace($html, $block, $stay = true) {

        echo (!Core::isAjax() ? $html : Html::ReplaceHtml($html, $block));
        $stay || $this->terminate();;
    }

    /**
     * Renders a HTML appending
     * the content into a element
     *
     * @param   string      $html   - The HTML content
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitAdd($html, $block, $stay = true) {

        echo (!Core::isAjax() ? $html : Html::AddHtml($html, $block));
        $stay || $this->terminate();
    }

    /**
     * Shows a hidden element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitShow($block, $stay = true) {
        echo Html::ShowHtml($block);
        $stay || $this->terminate();
    }

    /**
     * Hides a element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitHide($block, $stay = true) {
        echo Html::HideHtml($block);
        $stay || $this->terminate();
    }

    /**
     * Scrolls to an element
     *
     * @param   string      $element    - The element
     * @param   string      $speed      - The scroll speed
     * @param   bool        $stay       - If it should not finish execution after rendering
     */
    protected function scrollToElement($element, $speed = '1000', $stay = true) {
        echo (!Core::isAjax() ?
            '<script>$("html, body").animate({scrollTop: $("'.$element.'").offset().top}, ' . $speed . ');</script>' :
            '$("html, body").animate({scrollTop: $("'.$element.'").offset().top}, ' . $speed . ');'
        );
        $stay || $this->terminate();
    }

    /**
     * Preventing Memory Leaks
     */
    protected function terminate() {

        unset($this->view);
        unset($this->model);
        unset($this);
        exit;
    }

}