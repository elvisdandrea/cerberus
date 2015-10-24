<?php

/**
 * Class View
 *
 * The template Renderer
 *
 * This is using Smarty as renderer. There's a fork in which
 * Twig is being tested.
 *
 * This renderer allows to easily manipulate the template variables
 * and the template itself. To switch to a different template, all you
 * need is change the name in the constructor or call setTemplateName()
 *
 * This view also creates the globals with the template locations.
 * You can also add module javascript if necessary just by calling
 * appendJs() and specify its name, the location is automatically
 * identified.
 *
 * @author  Elvis D'Andrea
 * @email   <elvis.vista@gmail.com>
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
     * When you need to execute Javascript functions with specified parameters
     *
     * @var
     */
    private $jsFunctions = array();

    /**
     * If this request must append
     * the system JS files
     *
     * @var bool
     */
    private $systemJs = false;

    /**
     * When you need to append a generic template Javascript to the committed HTML
     *
     * @var array
     */
    private $templateJsFiles = array();

    private $pageTitle = array();

    /**
     * The constructor
     *
     * It instances the Smarty class
     * and sets the template location
     */
    public function __construct() {

        $this->setTemplateName(defined('CONTROLLER_TEMPLATE') ? CONTROLLER_TEMPLATE : TEMPLATE);      //The Template Name
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(TPLDIR . '/' . $this->templateName);
        $this->smarty->setCompileDir(IFCDIR . '/cache');

        defined('T_URL')     || define('T_URL',     MAINURL . '/tpl/' . $this->templateName );
        defined('T_CSSURL')  || define('T_CSSURL',  MAINURL . '/tpl/' . $this->templateName . '/res/css' );
        defined('T_JSURL')   || define('T_JSURL',   MAINURL . '/tpl/' . $this->templateName . '/res/js' );
        defined('T_IMGURL')  || define('T_IMGURL',  MAINURL . '/tpl/' . $this->templateName . '/res/img' );
        defined('T_FONTURL') || define('T_FONTURL', MAINURL . '/tpl/' . $this->templateName . '/res/fonts' );

        defined('T_CSSDIR')  || define('T_CSSDIR',  MAINDIR . '/tpl/' . $this->templateName . '/res/css' );
        defined('T_JSDIR')   || define('T_JSDIR',   MAINDIR . '/tpl/' . $this->templateName . '/res/js' );
        defined('T_IMGDIR')  || define('T_IMGDIR',  MAINDIR . '/tpl/' . $this->templateName . '/res/img' );
        defined('T_FONTDIR') || define('T_FONTDIR', MAINDIR . '/tpl/' . $this->templateName . '/res/fonts' );

    }

    /**
     * Appends all injected JS files into template
     *
     * @return string
     */
    public function injectJSFiles() {

        if (count($this->jsFiles) == 0 &&
            count($this->templateJsFiles) == 0 &&
            count($this->jsFunctions) == 0 &&
            !$this->systemJs) return '';

        $result = array();

        if ($this->systemJs) {
            $result[] = '<script src="'. JSURL . '/jquery.js"></script>';
            $result[] = '<script src="'. JSURL . '/md5.js"></script>';
            $result[] = '<script src="'. JSURL . '/html.js"></script>';
            $result[] = '<script src="'. JSURL . '/main.js"></script>';
        }

        foreach ($this->jsFiles as $jsFileName) {
            $jsFileName =  T_URL. '/' . $this->moduleName . '/js/' . $jsFileName . '.js';
            $result[] = '<script src="'. $jsFileName. '"></script>';
        }

        foreach ($this->templateJsFiles as $jsFileName) {
            $jsFileName =  T_JSURL . '/' . $jsFileName . '.js';
            $result[] = '<script src="'. $jsFileName. '"></script>';
        }

        foreach ($this->jsFunctions as $jsFunction) {
            $result[] = '<script>' . $jsFunction . '</script>';
        }

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
     * Adds a js file to be committed along with HTML
     *
     * @param   string      $jsFile     - The JS file name in the module template folder
     */
    public function appendTemplateJs($jsFile) {
        $this->templateJsFiles[] = $jsFile;
    }

    /**
     * Set to append SPA required JS
     */
    public function appendSystemJs() {
        $this->systemJs = true;
    }

    /**
     * Adds a JS function to be executed with
     * specified parameters
     *
     * @param   string      $name       - The function name
     * @param   array       $params     - Function parameters values
     */
    public function appendJsFunction($name, array $params = array()) {

        array_walk($params, function(&$item){
            $item = '\'' . String::buildStringNewLines($item) . '\'';
        });

        $this->jsFunctions[] = $name . '(' . implode(',', $params) . ');';
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

    public function loadSystemTemplate($module, $name) {

        $name = $module . '/' . $name;
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
     * Sets the page_title variable on the layout
     *
     * You must place this variable in a position you
     * want to display the current page title
     *
     * @param   string  $title          - The title
     * @param   string  $description    - The page description
     * @return  string
     */
    public function setPageTitle($title, $description) {

        $this->pageTitle = array(
            'title'         =>  $title,
            'description'   => $description
        );
    }

    /**
     * Displays an alert box
     *
     * @param   string  $type       - The alert type
     * @param   string  $title      - Title of the box
     * @param   string  $content    - Content of the box
     * @return  string              - The rendered box html
     */
    public function showAlert($type, $title, $content) {
        $types = array(
            'warning'       => 'alert-warning',
            'error'         => 'alert-danger',
            'info'          => 'alert-info',
            'danger'        => 'alert-danger',
            'success'       => 'alert-success',
            'dismissable'   => 'alert-dismissable'
        );

        isset($types[$type]) || $type = 'warning';

        $this->template = 'ifc/alert.tpl';

        $this->setVariable('title',   $title);
        $this->setVariable('content', $content);
        $this->setVariable('type',    $types[$type]);

        return $this->render();
    }

    /**
     * Renders a template
     *
     * @param   bool        $fetch          - Just return the html instead of rendering directly on screen
     * @param   bool        $changeTitle    - If it must change the title to the module's title
     * @return  string
     */
    public function render($fetch = true, $changeTitle = true) {

        try {
            $method = $fetch ? 'fetch' : 'display';

            if ($changeTitle && count($this->pageTitle) > 0) {
                $setTitle = Html::ReplaceHtml($this->pageTitle['title'], '#page_title') .
                    Html::ReplaceHtml($this->pageTitle['description'] . APPTITLE, 'title');

                if    (Core::isAjax()) echo $setTitle;
            }

            $hasJs = (count($this->jsFiles) > 0 || count($this->templateJsFiles) > 0) || $this->systemJs || count($this->jsFunctions) > 0;
            return $this->smarty->$method($this->template) . ($hasJs ? $this->injectJSFiles() : '');
        } catch (Exception $e) {
            echo Html::ReplaceHtml(ExceptionHandler::throwException(array(
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine()
            )), '#content');
            exit;
        }
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