<?php

/**
 * Class Control
 *
 * The App Controller Class
 *
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
        $this->post = $_POST;
        $this->get  = $_GET;
    }

    /**
     * Returns a post value
     *
     * @param   bool|string     $name       - the post field name
     * @return  mixed
     */
    protected function getPost($name = false) {
        if ($name) {
            return (isset($this->post[$name]) ? $this->post[$name] : false);
        }
        return $this->post;
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
     * Renders a HTML onto screen
     *
     * Still to be implemented
     *
     * @param $html
     */
    protected function commitPrint($html) {
        echo $html;
        exit;
    }

    /**
     * Renders a HTML replacing
     * the content of a element
     *
     * @param   string      $html   - The HTML content
     * @param   string      $block  - The element
     */
    protected function commitReplace($html, $block) {
        echo Html::ReplaceHtml($html, $block);
        exit;
    }

    /**
     * Renders a HTML appending
     * the content into a element
     *
     * @param   string      $html   - The HTML content
     * @param   string      $block  - The element
     */
    protected function commitAdd($html, $block) {
        echo Html::AddHtml($html, $block);
        exit;
    }

    /**
     * Shows a hidden element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitShow($block, $stay = false) {
        echo Html::ShowHtml($block);
        if (!$stay)
            exit;
    }

    /**
     * Hides a element
     *
     * @param   string      $block  - The element
     * @param   bool        $stay   - If it should not finish execution after rendering
     */
    protected function commitHide($block, $stay = false) {
        echo Html::HideHtml($block);
        if (!$stay)
            exit;
    }

}