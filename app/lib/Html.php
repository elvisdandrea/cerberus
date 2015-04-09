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
     * Returns the function to dinamically
     * set an element value
     *
     * @param   string      $value      - The value to be set
     * @param   string      $element    - The element
     * @return  string
     */
    public static function SetValue($value, $element) {
        return 'Html.SetValue(\'' . $element . '\',\'' . $value . '\');';
    }

    /**
     * Adds a class to an Element
     *
     * @param   string      $class      - The class name to be set
     * @param   string      $element    - The element
     * @return  string
     */
    public static function AddClass($class, $element) {
        return 'Html.AddClass(\'' . $element . '\',\'' . $class . '\');';
    }

    /**
     * Removes an Element class
     *
     * @param   string      $class      - The class name to be set
     * @param   string      $element    - The element
     * @return  string
     */
    public static function RemoveClass($class, $element) {

        return 'Html.RemoveClass(\'' . $element . '\',\'' . $class . '\');';
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
     * Validates if string is URL
     *
     * @param   string      $string
     * @return  bool
     */
    public static function isUrl($string) {

        $url = parse_url($string);
        return isset($url['scheme']) && in_array($url['scheme'], array('http', 'https'));
    }

    /**
     * Refreshes Current Page
     */
    public static function refresh() {

        echo 'window.location.href = "' . filter_input(INPUT_SERVER, 'HTTP_REFERER') . '"';
        exit;
    }



}

?>
