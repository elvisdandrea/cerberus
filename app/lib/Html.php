<?php

/**
 * Class HTML
 *
 * A library to manipulate Dynamic HTML
 *
 * This class returns the functions used by
 * the frontend to manipulate dynamic HTML
 * directly from the backend
 *
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 *
 */

class Html {

    /**
     * Returns the function to dynamically
     * append some HTML on screen
     *
     * @param   string      $html       - The inner HTML to be rendered
     * @param   string      $block      - The element to contain the HTML
     * @return  string
     */
    public static function AddHtml($html, $block) {

        return 'Html.Add(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';
    }

    /**
     * Returns the function to dynamically
     * replace some HTML on screen
     *
     * @param   string      $html       - The inner HTML to be rendered
     * @param   string      $block      - The element to contain the HTML
     * @return  string
     */
    public static function ReplaceHtml($html, $block) {

        return 'Html.Replace(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';
    }

    /**
     * Returns the function to dynamically
     * make some HTML visible
     *
     * @param   string      $block      - The element that contains the HTML
     * @return  string
     */
    public static function ShowHtml($block) {
        return 'Html.Show(\''.$block.'\');';
    }

    /**
     * Returns the function to dynamically
     * make some HTML invisible
     *
     * @param   string      $block      - The element that contains the HTML
     * @return  string
     */
    public static function HideHtml($block) {
        return 'Html.Hide(\''.$block.'\');';
    }

    /**
     * Still to be implemented
     *
     */
    public static function PushHtml($html) {
        return $html;
    }

    /**
     * Function to Asynchronously load
     * a select input content
     *
     * @param   string      $id     - The select input Id
     * @return  string
     */
    public static function AsyncLoadList($id) {
        return 'Html.AsyncLoadList(\'' . $id . '\');';
    }

    /**
     * Refreshes Current Page
     */
    public static function refresh() {

        echo 'window.location.href = "' . $_SERVER['HTTP_REFERER'] . '"';
        exit;
    }

    /**
     * A static function to statically get
     * a POST value when not in controllers
     *
     * Not Recommended, really
     *
     * @param   string      $name       - The field name in the POST
     * @return  bool
     */
    public static function GetPost($name) {

        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return false;
    }

    /**
     * Writes a value in a SESSION position
     *
     * Runs statically to be called when not in controllers
     *
     * All indexes and sub-indexes should be passed
     * in arguments, the last argument will be the value
     */
    public static function WriteSession() {
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
    public static function ReadSession() {
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
    public static function DeleteSession() {
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

?>
