<?php

/**
 * Class ftp
 *
 * Class for manipulating ftp transfer
 */
class Ftp {

    /**
     * The Resource Connector
     *
     * @var resource
     */
    private $conn;

    /**
     * The server name
     *
     * @var string
     */
    private $server;

    /**
     * The login user name
     *
     * @var string
     */
    private $user;

    /**
     * The login password
     *
     * @var string
     */
    private $pass;

    /**
     * Save error messages
     *
     * @var array
     */
    private $errors = array();

    /**
     * The transfer Data Mode
     *
     * @var int
     */
    private $transferData = FTP_BINARY;

    /**
     * The transfer Mode
     *
     * @var boolean
     */
    private $passv = false;


    /**
     * Folders or files to skip when copying a directory
     *
     * @var array
     */
    private $skipFiles = array('.', '..');

    /**
     * The constructor
     *
     * @param   string      $server        - URL to connect
     */
    public function __construct($server = ''){

        $this->setServer($server);
    }

    /**
     * Sets the FTP server
     *
     * @param $server
     */
    public function setServer($server) {
        $this->server = $server;
    }

    /**
     * Sets the FTP user and pass
     *
     * @param string    $user
     * @param string    $pass
     */
    public function setLogin($user, $pass) {
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Connects and authenticates
     */
    public function connect() {
        $this->conn = ftp_connect($this->server);
        $this->ftp_login($this->user, $this->pass);
    }

    /**
     * Getter for errors property
     *
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Sets Passive/Active Transfer Mode
     *
     * @param bool $passv
     */
    public function setPassvMode($passv) {
        $this->passv = $passv;
    }

    /**
     * Add a folder or file to skip when copying a directory
     *
     * @param   string      $file     - The folder or file name
     */
    public function addSkipFile($file) {
        $this->skipFiles[] = $file;
    }

    /**
     * Magic call for FTP functions using
     * this class resource
     *
     * This allows usage like this:
     *
     * $ftp = new ftp('ftp://example.com');
     * $ftp->ftp_login('username', 'password');
     * $ftp->ftp_put('source', 'destination');
     *
     * @param   string      $func       - The function name
     * @param   mixed       $a          - The function parameters
     * @return  mixed
     */
    public function __call($func,$a){

        if(strstr($func,'ftp_') !== false && function_exists($func)){
            array_unshift($a,$this->conn);
            return call_user_func_array($func,$a);
        } else {
            Core::throwError('Internal Invalid call: ' . $func);
        }
    }

    /**
     * Transfer an entire folder to a FTP Server
     *
     * @param   string      $src_dir        - The source directory to be copied
     * @param   string      $dst_dir        - The destination directory in the server
     */
    public function putAll($src_dir, $dst_dir) {

        $d = dir($src_dir);
        while($file = $d->read()) {
            if (!in_array($file, $this->skipFiles)) {
                if (is_dir($src_dir . '/' . $file)) {
                    if (!@$this->ftp_chdir('/' . $dst_dir . '/' . $file)) {
                        $this->ftp_mkdir('/' . $dst_dir . '/' . $file);
                    }
                    $this->putAll($src_dir . '/' . $file, $dst_dir . '/' . $file);
                } else {
                    $this->ftp_chdir('/' . $dst_dir);
                    $upload = $this->ftp_put($file, $src_dir . '/' . $file, $this->transferData);
                    if (!$upload) {
                        $error = error_get_last();
                        $this->errors[] = array(
                            'file'          => $file,
                            'location'      => $src_dir,
                            'destination'   => $dst_dir,
                            'error'         => $error['message']
                        );

                    }

                }
            }
        }
        $d->close();
    }

}