<?php

/**
 * File Manager Class
 *
 * Manipulation of System Directories and files
 */
class FileManager {

    /**
     * Creates directories recursively
     *
     * @access public
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    public static function rmkdir($path, $mode = 0777) {
        return is_dir($path) || ( self::rmkdir(dirname($path), $mode) && self::_mkdir($path, $mode) );
    }

    /**
     * Creates directory
     *
     * @access private
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    private static function _mkdir($path, $mode = 0777) {
        $old = umask(0);
        $res = @mkdir($path, $mode);
        umask($old);
        return $res;
    }

    /**
     * Incomplete
     *
     * @param $filename
     * @param $base64
     */
    public static function saveBase64File($filename, $base64) {

        $path = realpath($filename);
        self::rmkdir($path);
        file_put_contents($filename, base64_decode($base64));
    }

    /**
     * Why is it in a function when it's just unlink it?
     * - Because, in the future, it will not just unlink it
     *
     * @param $filename
     */
    public static function removeFile($filename) {

        @unlink($filename);
    }


}