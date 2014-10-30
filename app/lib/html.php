<?php

/*
 * leet.eti.br HTML LIBRARY
 * 
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 * 
 */

define('LIB_HTML', '1');

class Html {

    public static function AddHtml($html, $block) {

        return 'Html.Add(\'' . String::RemoveNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';
    }
    
    public static function ReplaceHtml($html, $block) {

        return 'Html.Replace(\'' . String::RemoveNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\');';
    }

    #public static function ShowHtml($html, $block, $window) {
    #    return 'Html.Show(\'' . String::RemoveNewLines(String::AddSQSlashes($html)) . '\',\'' . $block . '\',\'' . $window . '\');';
    #}

    public static function ShowHtml($block) {
        return 'Html.Show(\''.$block.'\');';
    }

    public static function HideHtml($block) {
        return 'Html.Hide(\''.$block.'\');';
    }

    public static function PushHtml($html) {
        return $html;
    }
    
    public static function GetPost($name) {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return false;
    }

    public static function WriteSession($name, $value) {
        $_SESSION[$name] = $value;
    }

    public static function AppendSession($path, $value) {        
        $_SESSION[$path][] = $value;       
    }

    public static function ReadSession($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return '';
    }

    public static function DeleteSession($name) {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }

}

?>
