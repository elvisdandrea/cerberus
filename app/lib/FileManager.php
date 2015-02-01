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


}