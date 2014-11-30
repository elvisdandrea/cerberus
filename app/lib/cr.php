<?php


/**
 * Class CR_
 *
 * Sorry, not many docs from here
 *
 * The encryption hash is generated dynamically,
 * so only this class can decrypt what's encrypted from here
 *
 * Just use CR_::get($string) to Encrypt or Decrypt anything
 * or CR_::encrypt($string), CR_::decrypt($string)
 */
class CR_ {

    /**
     * If encrypted, decrypts
     * If plain text, encrypts
     *
     * Simple as that
     *
     * @param   string      $str    - The original string
     * @return  string              - the encrypted/decrypted string
     */
    public static function get($str)
    {

        if (is_string($str)) {
            if (substr($str, 0, 1) == '!') {
                return self::decrypt($str);
            } else {
                return self::encrypt($str);
            }
        }
    }

    /**
     * Encrypts a text
     *
     * @param   string      $str        - Some plain text
     * @return  string                  - The encrypted text
     */
    public static function encrypt($str)
    {
        $pass = uniqid();

        srand((double) microtime() * 1000000);
        $td = mcrypt_module_open('des', '', 'cfb', '');
        $key = substr($pass, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        if (mcrypt_generic_init($td, $key, $iv) != -1) {
            $c_t = mcrypt_generic($td, $str);

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            return '!' . $pass . '$' . base64_encode($iv . $c_t);
        } else {
            return false;
        }
    }

    /**
     * Decrypts a text
     *
     * @param   string      $str        - The encrypted text
     * @return  string                  - The decrypted text
     */
    public static function decrypt($str)
    {
        $str = substr($str, 1);
        $str = explode('$', $str);
        $pass = $str[0];
        $str = $str[1];
        $str = base64_decode($str);
        $lib = mcrypt_module_open('des', '', 'cfb', '');
        $iv_size = mcrypt_enc_get_iv_size($lib);
        $key = substr($pass, 0, mcrypt_enc_get_key_size($lib));
        $iv = substr($str, 0, $iv_size);
        $str = substr($str, $iv_size);

        if (mcrypt_generic_init($lib, $key, $iv) != -1) {
            $c_t = mdecrypt_generic($lib, $str);

            mcrypt_generic_deinit($lib);
            mcrypt_module_close($lib);

            return $c_t;
        } else {
            return false;
        }
    }

}
?>
