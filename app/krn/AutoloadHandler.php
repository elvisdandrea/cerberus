<?php


class AutoloadHandler {


    /**
     * Autoload Class Handler
     *
     * @param   $class_name
     * @return  mixed
     */
    public static function autoLoad($class_name) {

        foreach (array(
                     MODDIR . '/' . preg_replace('/Control|Model$/','',$class_name),
                     LIBDIR,
                     KRNDIR
                 ) as $dir) {
            $file = $dir . '/' . $class_name . '.php';
            if (file_exists($file))
                return require_once $file;

        }
    }


}