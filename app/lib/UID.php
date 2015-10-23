<?php

/**
 * Class UID
 *
 * The UID manipulator
 *
 * This class handles the instance
 * of the current logged user
 *
 */
class UID {

    /**
     * Tests if user is logged in
     *
     * @return bool
     */
    public static function isLoggedIn() {

        $uid = Session::get('uid');
        return is_array($uid) && isset($uid['db_connection']);
    }


    /**
     * Reads a UID Session Value
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
        } else {
            return Session::get('uid');
        }
        if (!empty($session)) {
            eval('!isset($_SESSION["uid"]' . $session . ') || $value = $_SESSION["uid"]' . $session . ';');
        }
        return $value;
    }

    /**
     * Writes a value in a UID position
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
        if (count($aux) >= 1) {
            if (count($aux) > 1) {
                $value = $aux[(count($aux) - 1)];
                unset($aux[(count($aux) - 1)]);
            }
            foreach ($aux as $v) {
                if ((is_string($v) || is_numeric($v)) && (!empty($v) && $v != "[]" )) {
                    $session .= "['" . $v . "']";
                }else if($v == "[]"){
                    $session .= "[]";
                } else {
                    $value = $v;
                }
            }
            if (is_object($value)) {
                $value = serialize($value);
            }
            eval('$_SESSION["uid"]' . $session . ' = $value;');
        }
    }

    /**
     * Deletes an UID Value
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
        } else {
            Session::del('uid');
        }
        if (!empty($session)) {
            eval('unset($_SESSION["uid"]' . $session . ');');
        }
        return $value;
    }

    /**
     * Logs out
     */
    public static function logout() {

        self::del();
        Html::refresh();
    }
}