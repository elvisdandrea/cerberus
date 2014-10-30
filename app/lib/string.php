<?php

/*
 * leet.eti.br STRING LIBRARY
 *
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 *
 */

class String {

    public static function ClearString( $string ) {

        $string = preg_replace( '/<[^>]*>/', ' ', $string );
        $string = stripslashes( $string );
        $string = htmlspecialchars( $string, ENT_QUOTES );
        $string = str_replace( '"', '&quot;', $string );
        $string = str_replace( "'", "&#039;", $string );

        return $string;
    }

    /*
    public static function ClearArray( &$array ) {
        foreach ( $array as $value ) {
            if ( is_array( $value ) ) {
                $array[$value] = self::ClearArray( $value );
            } else {
                $array[$value] = self::ClearString( $value );
            }
        }
    }
*/
    public static function ClearObject( &$object ) {
        foreach ( $object as $value ) {
            if ( is_object( $value ) ) {
                $object->$value = self::ClearObject( $value );
            } else {
                $object->$value = self::ClearString( $value );
            }
        }
    }

    public static function StrToArrayValues( $str, $separator = ';', $quoted = false) {

        $array_result = array();
        $base = explode( $separator, $str );
        $quote = '';
        if ($quoted) {
            $quote = '"';
        }
        foreach ( $base as $item ) {
            if ( trim( $item ) != '' ) {
                $ar = explode( '=', $item );
                if ( count( $ar ) == 2 ) {
                    $array_result[trim( $ar[0] )] = trim( $quote . $ar[1] . $quote );
                } else {
                    $array_result[trim( $ar[0] )] = '';
                }
            }
        }
        return $array_result;
    }

    public static function RemoveNewLines( $string ) {
        return preg_replace( '/\s+/', ' ', trim( $string ) );
    }

    public static function AddSQSlashes( $string ) {
        return str_replace( '\'', '\\\'', $string );
    }

    public static function formatDateToSave($date) {
        if (strpos($date, '/') !== false) {
            $date = explode('/', $date);
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        }
        return '0000-00-00';
    }

    public static function formatDateTimeToSave($date, $time) {
        $date = self::formatDateToSave($date);
        if (strpos($time, ':') === false) $time = '00:00:00';
        return $date . ' ' . $time;
    }

    public static function formatDateToLoad($date) {
        if (strpos($date, '-') !== false) {
            $date = explode('-', $date);
            return $date[2] . '/' . $date[1] . '/' . $date[0];
        }
        return '00/00/0000';
    }

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

}

?>
