<?php

/**
 * Class View
 *
 * The template Renderer
 *
 */
class View {

    /**
     * The Smarty Class
     *
     * @var Smarty
     */
    private $smarty;

    /**
     * The Template name
     *
     * @var string
     */
    private $template;

    /**
     * The constructor
     *
     * It instances the Smarty class
     * and sets the template location
     */
    public function __construct() {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(TPLDIR);
    }


    /**
     * Loads a template file
     *
     * @param   string      $name       - The template name
     */
    public function loadTemplate($name) {
        
        $this->template = $name . '.tpl';
    }

    /**
     * Sets a variable in the template
     *
     * @param   string      $name   - The variable name
     * @param   string      $value  - The value
     */
    public function setVariable($name, $value) {
        
        $this->smarty->assign($name, $value);
    }

    /**
     * Renders a template
     *
     * @return string
     */
    public function render() {
        
        return $this->smarty->fetch($this->template);
    }
    
}