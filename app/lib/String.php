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

        return filter_var($string, FILTER_SANITIZE_URL, FILTER_SANITIZE_MAGIC_QUOTES);
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
     * @param   string|bool $time       - The original time in hh:mm:ss
     * @return  string                  - The string formatted to yyyy-mm-dd hh:mm:ss
     */
    public static function formatDateTimeToSave($date, $time = false) {

        if (!$time) {
            $date = str_replace('+', ' ', $date);
            $data = explode(' ', $date);
            if (count($data) > 0) {
                $date = $data[0];
                $time = $data[1];
            }
        }

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

        $val = preg_replace('/[^a-z0-9]/i','', $val);
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
     * Converts to phone format
     *
     * @param   string      $texto
     * @return  string
     */
    public static function phoneFormat($texto) {

        $result = '';
        $s1 = $string = preg_replace('/[^0-9]/i','', $texto);
        $p = 0;
        for ($i = strlen($s1); $i > 0; $i--) {
            $p++;
            switch ($p) {
                case 5: $result = '-' . $result;
                    break;
                case 9: if (strlen($s1) == 10 || strlen($s1) == 12)
                    $result = ' ' . $result;
                    break;
                case 10: if (strlen($s1) == 11 || strlen($s1) == 13)
                    $result = ' ' . $result;
            }
            $result = $s1[$i - 1] . $result;
        }
        return ($result == "" ? $texto : $result);
    }

    /**
     * Converte o texto do campo conforme opcao
     *
     * @param   string      $string         - O conteúdo do campo
     * @param   int         $option         - A opcao
     * @return  string
     */
    public static function convertTextFormat($string, $option) {
        switch ($option) {
            case 'ln': //Letras e Números
                $string = preg_replace('/[^a-z0-9\-]/i','',$string);
                break;
            case 'l': //Letras
                $string = preg_replace('/convertTe[^a-z\-]/i','',$string);
                break;
            case 'n': //Números
                $string = preg_replace('/[^0-9\-]/i','',$string);
                break;
            case 'fone': //Telefone
                $string = self::phoneFormat($string);
                break;
            case 'ddd': //Telefone com DDD
                $string = self::phoneFormat($string);
                break;
            case 'cnpj': //CNPJ
                $string = self::mask($string, '##.####.###/####-##');
                break;
            case 'cpf': //CPF
                $string = self::mask($string, '###.###.###-##');
                break;
            case 'cep': //CEP
                $string = self::mask($string, '#####-###');
                break;
            case 'currency':
                $string = 'R$ ' . number_format($string, 2, ',', '.');
                break;
            case 'email': //E-MAIL
                // Nothing we can do here
                break;
            case 'int': //Numeros Inteiros
                $string = preg_replace('/[^0-9\-]/i','',$string);
                break;
            case 'float':
                $string = number_format($string, 2, ',', '.');
                break;
        }

        return $string;
    }

    /**
     * Valida o formato dos dados em um campo de texto
     *
     * @param   string      $string     - O conteúdo
     * @param   int         $option     - O valor da opcao
     * @return  bool
     */
    public static function validateTextFormat($string, $option) {

        switch ($option) {
            case 'ln': //Letras e Números
                return preg_match('/[a-z0-9]/i', $string);
                break;
            case 'l': //Letras
                return preg_match('/[a-z]/i', $string);
                break;
            case 'n': //Números
                return preg_match('/[0-9]/i', $string);
                break;
            case 'fone': //Telefone
                return preg_match('/^([0-9]{2}\s[0-9]{4}\-[0-9]{4})|([0-9]{2}\s[0-9]{5}\-[0-9]{4})$/', $string);
                break;
            case 'ddd': //Telefone com DDD
                return preg_match('/^([0-9]{2}\s[0-9]{4}\-[0-9]{4})|([0-9]{2}\s[0-9]{5}\-[0-9]{4})$/', $string);
                break;
            case 'cnpj': //CNPJ
                return preg_match('/^(\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/', $string);
                break;
            case 'cpf': //CPF
                return preg_match('/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/', $string);
                break;
            case 'cnpjcpf': //CPNJ ou CPF
                return preg_match('/(^\d{3}\.\d{3}\.\d{3}\-d{2}$)|(^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$)/', $string);
                break;
            case 'cep': //CEP
                return preg_match('/^(\d{5}\-\d{3})$/', $string);
                break;
            case 'email': //E-MAIL
                return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{1,3})+$/', $string);
                break;
            case 'int': //Numeros Inteiros
                return preg_match('/[0-9\-]/', $string);
                break;
            case 'float':
                return preg_match('/^[0-9]+\.?[0-9]*$/', $string);
                break;
            default:
                return true;
                break;
        }
    }

    public static function cpfValid($cpf) {

        $cpf = preg_replace('/^[a-z0-9]/i', '', $cpf);
        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
    }

    /**
     * Checks for a valid CPF
     *
     * Not just if the format matches, but
     * really checks using the code validator
     *
     * @param   string      $cpf        - The CPF
     * @return  bool
     */
    public static function validateCpf($cpf){
        if(empty($cpf))
            return true;

        $cpf = preg_replace('/[\-\.]/','',$cpf);
        $cpf = str_split($cpf);

        if(count($cpf) != 11)
            return false;

        $sum1 = ($cpf[0] * 10) + ($cpf[1] * 9) + ($cpf[2] * 8) + ($cpf[3] * 7) + ($cpf[4] * 6) + ($cpf[5] * 5) + ($cpf[6] * 4) + ($cpf[7] * 3) + ($cpf[8] * 2);

        $sum2 = ($cpf[0] * 11) + ($cpf[1] * 10) + ($cpf[2] * 9) + ($cpf[3] * 8) + ($cpf[4] * 7) + ($cpf[5] * 6) + ($cpf[6] * 5) + ($cpf[7] * 4) + ($cpf[8] * 3) + ($cpf[9] * 2);

        $mod1 = ($sum1 * 10) % 11;
        $mod1 != 0 || $mod1 = 0;

        $mod2 = ($sum2 * 10) % 11;
        $mod2 != 0 || $mod2 = 0;

        return $mod1 == $cpf[9] && $mod2 == $cpf[10];
    }

    /**
     * Checks for a valid CNPJ
     *
     * Not just if the format matches, but
     * really checks using the code validator
     *
     * @param   string      $cnpj        - The CNPJ
     * @return  bool
     */
    public static function validateCnpj($cnpj) {
        $cnpj = preg_replace('/[\-\.\/]/','',$cnpj);
        $cnpj = str_split($cnpj);

        if (count($cnpj) <> 14)
            return false;

        $sum1 = ($cnpj[0] * 5) + ($cnpj[1] * 4) + ($cnpj[2] * 3) + ($cnpj[3] * 2) + ($cnpj[4] * 9) + ($cnpj[5] * 8) + ($cnpj[6] * 7) + ($cnpj[7] * 6) + ($cnpj[8] * 5) + ($cnpj[9] * 4) + ($cnpj[10] * 3) + ($cnpj[11] * 2);

        $mod1 = $sum1 % 11;
        $mod1 = $mod1 < 2 ? 0 : 11 - $mod1;

        $sum2 = ($cnpj[0] * 6) + ($cnpj[1] * 5) + ($cnpj[2] * 4) + ($cnpj[3] * 3) + ($cnpj[4] * 2) + ($cnpj[5] * 9) + ($cnpj[6] * 8) + ($cnpj[7] * 7) + ($cnpj[8] * 6) + ($cnpj[9] * 5) + ($cnpj[10] * 4) + ($cnpj[11] * 3) + ($cnpj[12] * 2);

        $mod2 = $sum2 % 11;
        $mod2 = $mod2 < 2 ? 0 : 11 - $mod2;

        return $cnpj[12] == $mod1 && $cnpj[13] == $mod2;
    }

    /**
     * Returns the month acronym from month number
     *
     * @param   int     $monthNumber        - The month nymber
     * @return  string                      - The month acronym
     */
    public function getMonthAcronym($monthNumber) {

        $months = array(
            '', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
        );

        return $months[intval($monthNumber)];
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
