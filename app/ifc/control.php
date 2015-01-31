<?php

/**
 * Class Control
 *
 * The App Controller Class
 *
 */

class Control {

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
     * Thou shalt not call superglobals directly
     * even though I'm doing it in this function
     */
    public function __construct() {
        $this->post = String::ClearArray($_POST);
        $this->get  = String::ClearArray($_GET);
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
        foreach ($args as $arg) {
            !isset($this->post[$arg]) || $result[$arg] = $this->post[$arg];
        }

        return $result;
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

        foreach ($args as $arg) {
            if (!isset($this->post[$arg]) || $this->post[$arg] == '')
                return false;
        }
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
        if ($name) {
            return (isset($this->get[$name]) ? $this->get[$name] : false);
        }
        return $this->get;
    }

    /**
     * Preventing Memory Leaks
     */
    protected function terminate() {

        unset($this);
        exit;
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
    protected function commitReplace($html, $block, $stay = false) {
        if (!Core::isAjax()) {
            echo $html;
        } else {
            echo Html::ReplaceHtml($html, $block);
        }
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
    protected function commitAdd($html, $block, $stay = false) {
        if (!Core::isAjax()) {
            echo $html;
        } else {
            echo Html::AddHtml($html, $block);
        }
        $stay || $this->terminate();;
    }

    /**
     * Shows a hidden element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitShow($block, $stay = false) {
        echo Html::ShowHtml($block);
        $stay || $this->terminate();;
    }

    /**
     * Hides a element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitHide($block, $stay = false) {
        echo Html::HideHtml($block);
        $stay || $this->terminate();;
    }

    /**
     * Scrolls to an element
     *
     * @param   string      $element    - The element
     * @param   string      $speed      - The scroll speed
     * @param   bool        $stay       - If it should not finish execution after rendering
     */
    protected function scrollToElement($element, $speed = '1000', $stay = false) {
        if (Core::isAjax()) {
            echo '$("html, body").animate({scrollTop: $("'.$element.'").offset().top}, ' . $speed . ');';
            $stay || $this->terminate();;
        } else {
            echo '<script>$("html, body").animate({scrollTop: $("'.$element.'").offset().top}, ' . $speed . ');</script>';
        }
    }

}