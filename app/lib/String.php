<?php

/**
 * Class String
 *
 * A library to manipulate and convert string values
 *
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 *
 */

class String {

    /**
     * Preventing string from having string injections
     *
     * @param   string      $string     - The original string
     * @return  string                  - The escaped string
     */
    public static function ClearString( $string ) {
        //TODO: It's begging for a real anti-injection algorith

        #$string = mysql_real_escape_string($string);
        $string = addslashes($string);
        return $string;
    }


    /**
     * Cleans an entire array recursively
     * from having string injection
     *
     * @param   array       $array      - The original array
     * @return  array                   - The escaped array
     */
    public static function ClearArray( $array ) {
        array_walk_recursive($array, function(&$item){
            $item = String::ClearString($item);
        });
        return $array;
    }

    /**
     * Remove new line characters from a string
     *
     * @param   string      $string     - The original string
     * @return  string                  - The string without new lines
     */
    public static function RemoveNewLines( $string ) {
        return preg_replace( '/\s+/', ' ', trim( $string ) );
    }

    /**
     * Concatenates each line of a string into slashes
     * and a concatenation character
     *
     * @param   string    $string           - The original string
     * @param   string    $concat_char      - The concatenation character
     * @return  string                      - The converted string
     */
    public static function BuildStringNewLines( $string, $concat_char = '+' ) {
        return preg_replace( '/' . PHP_EOL . '+/', '\'' . PHP_EOL . $concat_char .'"\\' . PHP_EOL .'"' . $concat_char . '\'', trim( $string ));
    }

    /**
     * An "addslashes" for single quotes only
     *
     * @param   string      $string     - The original string
     * @return  string
     */
    public static function AddSQSlashes( $string ) {
        return str_replace( '\'', '\\\'', $string );
    }

    /**
     * A non-validation version to format string dates
     * from dd/mm/yyyy to yyyy-mm-dd
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $date       - The original string date in dd/mm/yyyy format
     * @return  string                  - The string formatted to yyyy-mm-dd
     */
    public static function formatDateToSave($date) {
        if (strpos($date, '/') !== false) {
            $date = explode('/', $date);
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        }
        return '0000-00-00';
    }

    /**
     * A non-validation version to format string dates
     * from dd/mm/yyyy hh:mm:ss to yyyy-mm-dd hh:mm:ss
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $date       - The original string date in dd/mm/yyyy format
     * @param   string      $time       - The original time in hh:mm:ss
     * @return  string                  - The string formatted to yyyy-mm-dd hh:mm:ss
     */
    public static function formatDateTimeToSave($date, $time) {
        $date = self::formatDateToSave($date);
        if (strpos($time, ':') === false) $time = '00:00:00';
        return $date . ' ' . $time;
    }

    /**
     * A non-validation version to format string dates
     * from yyyy-mm-dd to dd/mm/yyyy
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $date       - The original string date in yyyy-mm-dd format
     * @return  string                  - The string formatted to dd/mm/yyyy
     */
    public static function formatDateToLoad($date) {
        if (strpos($date, '-') !== false) {
            $date = explode('-', $date);
            return $date[2] . '/' . $date[1] . '/' . $date[0];
        }
        return '00/00/0000';
    }

    /**
     * A non-validation version to format string dates
     * from yyyy-mm-dd hh:mm:ss to dd/mm/yyyy hh:mm:ss
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $datetime       - The original string date in yyyy-mm-ddd hh:mm:ss format
     * @param   string      $separator      - Character to separate the date from time (optional)
     * @return  string                      - The string formatted to dd/mm/yyyy hh:mm:ss
     */
    public static function formatDateTimeToLoad($datetime, $separator = '') {
        $date = explode(' ',$datetime);
        if (count($date) > 1) {
            $fdate = self::formatDateToLoad($date[0]);
            $ftime = explode(':', $date[1]);
            $ftime = $ftime[0] . ':' . $ftime[1];
            return $fdate . ' ' . $separator. ' ' . $ftime;
        }
        return '00/00/0000 '.$separator.' 00:00';
    }

    /**
     * Applies any mask based on # character
     *
     * Such a wow I just did
     *
     * @param   string      $val        - The number value
     * @param   string      $mask       - The desired mask
     * @return  string
     */
    public static function mask($val, $mask) {

        $val = preg_replace('/[^a-z0-9\-]/i','', $val);
        $masked = '';
        $k = strlen($val) - 1;
        for($i = strlen($mask)-1; $i>=0; $i--) {
            if ($k < 0) break;
            $mask[$i] != '#' || $masked = $val[$k--] . $masked;
            $mask[$i] == '#' || $masked = $mask[$i]  . $masked;
        }
        return $masked;
    }

    /**
     * Removes empty values for arrays
     * with numeric indexes
     *
     * The indexes that contain values will
     * be moved upwards, so numeric indexes
     * will remain in sequence
     *
     * @param   array       $array      - The original array
     */
    public static function arrayTrimNumericIndexed(&$array) {

        $result = array();

        foreach ($array as $value) $value == '' || $result[] = $value;
        $array = $result;
    }

    /**
     * Converts CameCase text to Uppercase-First-Letter words
     *
     * @param   string      $word           - The CamelCased Text
     * @return  string                      - The Uppercase-First-Letter text
     */
    public static function decamelize($word) {
        return preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strlen("\\1") ? "\\1 \\2" : "\\2"',
            $word
        );
    }

    /**
     * Converts underline_separated_text to CamelCase Text
     *
     * @param   string      $word           - The underlined_separated_text
     * @return  string                      - The CamelCased text
     */
    public static function camelize($word) {
        return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $word);
    }

}

?>
