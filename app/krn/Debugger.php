<?php

/**
 * Class Debugger
 *
 *
 */
class Debugger {

    /**
     * Dumps anything you need to see
     *
     * @param   mixed       $mixed      - What to debug
     * @param   string      $element    - Where to render it
     * @return  string
     */
    public static function debug($mixed, $element = 'html') {

        //TODO: RESTful debug

        $trace = debug_backtrace();
        $view = new View();
        $view->setModuleName('krn');
        $view->loadTemplate('debug');
        $view->setVariable('element', $element);
        $view->setVariable('trace', $trace);
        $view->setVariable('mixed', $mixed);

        $result = $view->render();

        !Core::isAjax() ||
            $result = Html::ReplaceHtml($result, $element);

        return $result;
    }

}