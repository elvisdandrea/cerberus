<?php

/**
 * Class Currency
 *
 * Currency Conversions
 */
class Currency {

    /**
     * API URL
     */
    const API_URL = 'http://api.fixer.io/';


    /**
     * Returns a list of currency rates
     *
     * @param   string      $base       - The rate base
     * @param   string      $date       - Which date to get rates
     * @return  mixed
     */
    public static function getRates($base = 'USD', $date = 'latest') {

        $ch = curl_init(self::API_URL . $date . '?base=' . $base);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }


    /**
     * Converts a value to specific rate
     *
     * @param   float|string    $value          - The value to convert
     * @param   string          $from           - The base currency
     * @param   string          $to             - The currency to convert to
     * @param   string          $rateDate       - Which date rate to use
     * @return  float
     */
    public static function convert($value, $from, $to, $rateDate = 'latest') {

        $rates = self::getRates($from, $rateDate);
        $rate  = $rates['rates'][$to];

        return $value * $rate;

    }


}