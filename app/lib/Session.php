<?php

class Session {

    /**
     * Writes a value in a SESSION position
     *
     * Runs statically to be called when not in controllers
     *
     * All indexes and sub-indexes should be passed
     * in arguments, the last argument will be the value
     */
    public static function set() {
        $aux = func_get_args();
        $session = "";
        $value = "";
        if (count($aux) >= 2) {
            $value = $aux[(count($aux) - 1)];
            unset($aux[(count($aux) - 1)]);
            foreach ($aux as $v) {
                if ((is_string($v) || is_numeric($v)) && (!empty($v) && $v != "[]" )) {
                    $session .= "['" . $v . "']";
                }else if($v == "[]"){
                    $session .= "[]";
                }
            }
            if (is_object($value)) {
                $value = serialize($value);
            }
            if (!empty($session)) {
                eval('$_SESSION' . $session . ' = $value;');
            }
        }
    }


    /**
     * Reads a Session Value
     *
     * Runs statically to be called when not in controllers
     *
     * All indexes and sub-indexes should be passed
     * in arguments
     *
     * @return  mixed|string
     */
    public static function get() {
        $aux = func_get_args();
        $session = "";
        $value = "";
        if (count($aux)) {
            foreach ($aux as $v) {
                if (is_string($v) || is_numeric($v)) {
                    $session .= "['" . $v . "']";
                }
            }
        }
        if (!empty($session)) {
            eval('$value = ""; !isset($_SESSION' . $session . ') || $value = $_SESSION' . $session . ';');
        }
        return $value;
    }

    /**
     * Deletes a Session Value
     *
     * Runs statically to be called when not in controllers
     *
     * All indexes and sub-indexes should be passed
     * in arguments
     *
     * @return  mixed|string
     */
    public static function del() {
        $aux = func_get_args();
        $session = "";
        $value = "";
        if (count($aux)) {
            foreach ($aux as $v) {
                if (is_string($v) || is_numeric($v)) {
                    $session .= "['" . $v . "']";
                }
            }
        }
        if (!empty($session)) {
            eval('unset($_SESSION' . $session . ');');
        }
        return $value;
    }
}