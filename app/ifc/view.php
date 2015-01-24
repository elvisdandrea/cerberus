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
     * The Template Name
     *
     * An easy way to switch between templates
     * The tpl folder should contain a list
     * of templates to be chosen
     *
     * @var string
     */
    private $templateName;

    /**
     * The Template files of
     * specific modules must be in a
     * subfolder with the module name
     *
     * @var string
     */
    private $moduleName;

    /**
     * When you need to append Javascript to the committed HTML
     *
     * @var array
     */
    private $jsFiles = array();

    /**
     * The constructor
     *
     * It instances the Smarty class
     * and sets the template location
     */
    public function __construct() {

        $this->setTemplateName('default');      //The default Template Name
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(TPLDIR . '/' . $this->templateName);
        $this->smarty->setCompileDir(IFCDIR . '/cache');

        $ref = new ReflectionClass($this);
        $moduleName = basename(dirname($ref->getFileName()));
        $this->setModuleName($moduleName);

        define('T_CSSURL',  MAINURL . '/tpl/' . $this->templateName . '/res/css' );
        define('T_JSURL',   MAINURL . '/tpl/' . $this->templateName . '/res/js' );
        define('T_IMGURL',  MAINURL . '/tpl/' . $this->templateName . '/res/img' );
        define('T_FONTURL', MAINURL . '/tpl/' . $this->templateName . '/res/fonts' );

        define('T_CSSDIR',  MAINDIR . '/tpl/' . $this->templateName . '/res/css' );
        define('T_JSDIR',   MAINDIR . '/tpl/' . $this->templateName . '/res/js' );
        define('T_IMGDIR',  MAINDIR . '/tpl/' . $this->templateName . '/res/img' );
        define('T_FONTDIR', MAINDIR . '/tpl/' . $this->templateName . '/res/fonts' );

    }

    /**
     * Appends all injected JS files into template
     *
     * @return string
     */
    private function injectJSFiles() {
        if (count($this->jsFiles) == 0) return '';

        $result = array();

        if (!Core::isAjax())
            $result[] = '<script>';

        /**
         * No need to check if file exists, let the handler "handle" it
         */
        foreach ($this->jsFiles as $jsFileName) {
            $jsFileName = TPLDIR . '/' . $this->templateName . '/' . $this->moduleName . '/js/' . $jsFileName . '.js';
            $content = file_get_contents($jsFileName);
            $result[] = $content;
        }

        if (!Core::isAjax())
            $result[] = '</script>';

        return implode(' ', $result);
    }

    /**
     * Adds a js file to be committed along with HTML
     *
     * @param   string      $jsFile     - The JS file name in the module template folder
     */
    public function appendJs($jsFile) {

        $this->jsFiles[] = $jsFile;
    }

    /**
     * Sets The Template Name
     *
     * @param   string      $name       - The Template Name
     */
    public function setTemplateName($name) {
        $this->templateName = $name;
    }

    /**
     * Sets The Module Name
     *
     * @param $name
     */
    public function setModuleName($name) {
        $this->moduleName = $name;
    }

    /**
     * Returns The Module Name
     *
     * @return  string      - The Module Name
     */
    public function getModuleName() {
        return $this->moduleName;
    }

    /**
     * Checks if a template file exists
     *
     * @param   string      $name       - The Template Name
     * @return  bool                    - True|False if the tpl file exists
     */
    public function templateExists($name) {

        if ($this->moduleName != '')
            $name = $this->moduleName . '/' . $name;

        return is_file(TPLDIR . '/' . $this->templateName . '/' . $name . '.tpl');
    }

    /**
     * Loads a template file
     *
     * @param   string      $name       - The template name
     */
    public function loadTemplate($name) {

        if (!$this->templateExists($name)) {
            $name = 'ifc/404';
        } elseif ($this->moduleName != '') {
            $name = $this->moduleName . '/' . $name;
        }

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

        return $this->smarty->fetch($this->template) . (count($this->jsFiles) > 0 ? $this->injectJSFiles() : '');
    }

    /**
     * Renders a 404 page
     *
     * @return string
     */
    public function get404() {

        $this->template = 'ifc/404.tpl';
        return $this->render();
    }

}