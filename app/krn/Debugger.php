<?php

class Debugger {

    public static function debug($mixed, $element = 'html') {

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