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
        if (Core::isAjax())
            return 'Html.Add(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';

        return '<script>Html.Add(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');</script>';
    }

    /**
     * Returns the function to dynamically
     * append a row to a table element
     *
     * @param   string      $html       - The inner HTML to be rendered
     * @param   string      $block      - The element to contain the HTML
     * @return  string
     */
    public static function AppendToTable($html, $block) {
        if (Core::isAjax())
            return 'Html.AppendToTable(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';

        return '<script>Html.AppendToTable(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');</script>';
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
        if (Core::isAjax())
            return 'Html.Replace(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';
    }

    /**
     * Returns the function to dynamically
     * remove some HTML element from screen
     *
     * @param   string      $block     - The element to remove
     * @return  string
     */
    public static function RemoveHtml($block) {
        if (Core::isAjax())
            return 'Html.Remove(\'' . $block . '\');';
    }

    /**
     * Returns the function to dynamically
     * replace some HTML on screen
     *
     * This one will wrap the function in script tag
     * in case we're not in ajax request
     *
     * @param   string      $html       - The inner HTML to be rendered
     * @param   string      $block      - The element to contain the HTML
     * @return  string
     */
    public static function renderHtml($html, $block) {
        if (Core::isAjax())
            return 'Html.Replace(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';

        return '<script>Html.Replace(\'' . String::BuildStringNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');</script>';
    }

    /**
     * Returns the function to dynamically
     * make some HTML visible
     *
     * @param   string      $block      - The element that contains the HTML
     * @return  string
     */
    public static function ShowHtml($block) {
        if (Core::isAjax())
            return 'Html.Show(\''.$block.'\');';

        return '<script>Html.Show(\''.$block.'\');</script>';
    }

    /**
     * Returns the function to dynamically
     * make some HTML invisible
     *
     * @param   string      $block      - The element that contains the HTML
     * @return  string
     */
    public static function HideHtml($block) {
        if (Core::isAjax())
            return 'Html.Hide(\''.$block.'\');';

        return '<script>Html.Hide(\''.$block.'\');</script>';
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
        if (Core::isAjax())
            return 'Html.SetValue(\'' . $element . '\',\'' . $value . '\');';

        return '<script>Html.SetValue(\'' . $element . '\',\'' . $value . '\');</script>';
    }

    /**
     * Adds a class to an Element
     *
     * @param   string      $class      - The class name to be set
     * @param   string      $element    - The element
     * @return  string
     */
    public static function AddClass($class, $element) {
        if (Core::isAjax())
            return 'Html.AddClass(\'' . $element . '\',\'' . $class . '\');';

        return '<script>Html.AddClass(\'' . $element . '\',\'' . $class . '\');</script>';
    }

    /**
     * Removes an Element class
     *
     * @param   string      $class      - The class name to be set
     * @param   string      $element    - The element
     * @return  string
     */
    public static function RemoveClass($class, $element) {
        if (Core::isAjax())
            return 'Html.RemoveClass(\'' . $element . '\',\'' . $class . '\');';

        return '<script>Html.RemoveClass(\'' . $element . '\',\'' . $class . '\');</script>';
    }

    /**
     * Scrolls page to an element
     *
     * @param   string      $element    - The element
     * @param   string      $speed      - The speed to reach the element
     * @return  string
     */
    public static function ScrollToElement($element, $speed = '1000') {
        return (!Core::isAjax() ?
            '<script>Html.ScrollToElement("' . $element . '","' . $speed . '");</script>' :
            'Html.ScrollToElement("' . $element . '","' . $speed . '");'
        );
    }

    /**
     * Still to be implemented
     *
     */
    public static function PushHtml($html) {
        return $html;
    }

    /**
     * Redirects to a destination URL
     *
     * @param   string      $url            - The destination URL
     * @param   bool        $changeUrl      - If it must change the browser URL
     * @return  string
     */
    public static function redirect($url, $changeUrl = true) {
        if (Core::isAjax())
            return 'Html.Redirect(\'' . $url . '\',\'' . $changeUrl . '\');';

        return '<script>Html.Redirect(\'' . $url . '\',\'' . $changeUrl . '\');</script>';
    }

    /**
     * Moves user to another page
     *
     * @param   string      $url        - The destination URL
     * @return  string
     */
    public static function SetLocation($url) {
        if (Core::isAjax())
            return 'Html.SetLocation(\'' . $url . '\');';

        return '<script>Html.SetLocation(\'' . $url . '\');</script>';
    }

    /**
     * Function to Asynchronously load
     * a select input content
     *
     * @param   string      $id         - The select input Id
     * @param   bool        $selected   - The selected option value
     * @return  string
     */
    public static function AsyncLoadList($id, $selected = false) {
        if (Core::isAjax())
            return 'Html.AsyncLoadList(\'' . $id . '\'' . ($selected ? ',' . $selected : '') . ');';

        return '<script>Html.AsyncLoadList(\'' . $id . '\'' . ($selected ? ',' . $selected : '') . ');</script>';
    }

    /**
     * Function that creates an action on an input[type="file"]
     * to upload an image, but it sets the src of an image
     * element with the base64 of the image to be sent via POST
     *
     * @param   string      $inputId        - The Id of the input type="file" element
     * @param   string      $imgId          - The Id of the Image element (must be inside a form to be sent in the POST)
     * @return  string
     */
    public static function addImageUploadAction($inputId, $imgId) {
        if (Core::isAjax())
            return 'Main.imageAction(\'' . $inputId . '\', \'' . $imgId . '\');';

        return '<script>Main.imageAction(\'' . $inputId . '\', \'' . $imgId . '\');</script>';
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
     * Logs string into console
     *
     * @param   $string
     */
    public static function logConsole($string) {
        if (Core::isAjax()) {
            echo 'console.log("' . $string . '");';
            return;
        }

        echo '<script>console.log("' . $string . '");</script>';
    }

    /**
     * Refreshes Current Page
     */
    public static function refresh() {
        if (Core::isAjax()) {
            echo 'window.location.href = "' . filter_input(INPUT_SERVER, 'HTTP_REFERER') . '"';
            return;
        }

        echo '<script>window.location.href = "' . filter_input(INPUT_SERVER, 'HTTP_REFERER') . '"</script>';
    }

    /**
     * Renders a Google Map inside an element
     *
     * @param   $elementId    - The element to contain the map
     * @param   $lat          - Start Latitude
     * @param   $lng          - Start Longitude
     * @param   $zoom         - Start Zoom
     */
    public static function initGMap($elementId, $lat, $lng, $zoom) {

        if (Core::isAjax()) {
            echo 'GMaps.init("' . $elementId . '", ' . $lat . ', ' . $lng . ', ' . $zoom . ');';
            return;
        }

        echo '<script>GMaps.init("' . $elementId . '", ' . $lat . ', ' . $lng . ', ' . $zoom . ');</script>';
    }

    /**
     * Adds a marker in a previously initiated Google Map
     *
     * @param   $elementId          - The element Id
     * @param   $lat                - Marker Latitude
     * @param   $lng                - Marker Longitude
     * @param   $contentString      - The Content String (for instance, a rendered view file)
     * @param   $contentTitle       - The Content Title
     */
    public static function addGMapMarker($elementId, $lat, $lng, $contentString, $contentTitle) {

        if (Core::isAjax()) {
            echo 'GMaps.addMarker("' . $elementId . '", ' . $lat . ', ' . $lng . ', "' . $contentString . '", "' . $contentTitle . '");';
            return;
        }

        echo '<script>GMaps.addMarker("' . $elementId . '", ' . $lat . ', ' . $lng . ', "' . $contentString . '", "' . $contentTitle . '");</script>';
    }

    /**
     * Draws a circle around a Latitude/Longitude in a initiated Google Map
     *
     * @param   $elementId      - The element Id
     * @param   $lat            - The centered Latitude
     * @param   $lng            - The centered Longitude
     * @param   $radius         - The circle raius
     */
    public static function addGMapCircle($elementId, $lat, $lng, $radius) {

        if (Core::isAjax()) {
            echo 'GMaps.addCircle("' . $elementId . '", ' . $lat . ', ' . $lng . ', ' . $radius . ');';
            return;
        }

        echo '<script>GMaps.addCircle("' . $elementId . '", ' . $lat . ', ' . $lng . ', ' . $radius . ');</script>';
    }


}

?>
