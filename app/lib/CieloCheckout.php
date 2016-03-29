<?php

/**
 * Class CieloCheckout
 *
 * This class generates the body of the request for
 * integrating with Cielo Checkout API
 *
 * @author  Elvis D'Andrea
 * @email   elvis.gravi@gmail.com
 */
class CieloCheckout {

    /**
     * This must be a json file with the information
     * for accessing the API
     *
     * E.g.:
     *      {
     *           "MerchantId"    : "6f80cf28-2ede-402e-9573-c23d1aa73abc",
     *           "checkoutUrl"   : "https://cieloecommerce.cielo.com.br/api/public/v1/orders"
     *       }
     */
    const CNFG_FILE         = 'conf/cielo.conf-3.json';

    /**
     * The Cielo Merchant ID
     *
     * @var string
     */
    private $merchantId     = '';

    /**
     * The Cielo Checkout API URL
     *
     * @var string
     */
    private $checkoutUrl    = '';

    /**
     * The container of the list of
     * errors that occurred during the process
     *
     * @var array
     */
    private $errors   = array();

    /**
     * Stores the request HTTP information
     *
     * @var array
     */
    private $info     = array();

    /**
     * Stores the execution response
     *
     * @var array
     */
    private $response = array();

    /**
     * The Request Body
     *
     * This will be encoded into JSON to be
     * submitted to the Checkout API
     *
     * @var array
     */
    private $jsonData = array(
        'OrderNumber'       => '',
        'SoftDescriptor'    => '',
        'Cart'              => array(
            'items'             => array()
        ),
        'Shipping'          => array(
            'type'              => 'WithoutShipping'
        ),
        'Payment'           => array(),
        'Customer'          => array(),
        'Options'           => array(
            'AntifraudEnabled'  => false
        )
    );

    /**
     * List of required data
     *
     * @var array
     */
    private $requirements = array(
        'OrderNumber'       => '',
        'SoftDescriptor'    => '',
        'Cart'              => array(
            'items'             => array(
                'Name', 'UnitPrice', 'Quantity', 'Type'
            )
        ),
        'Shipping'          => array(
            'type'
        )
    );

    /**
     * The list of valid Item Types
     *
     * @var array
     */
    private $validItemTypes = array(
        'Asset', 'Digital', 'Service', 'Payment'
    );

    /**
     * The list of valid Discount Types
     *
     * @var array
     */
    private $validDiscountTypes = array(
        'Percent', 'Amount'
    );

    /**
     * The list of valid Shipping Types
     *
     * @var array
     */
    private $validShippingTypes = array(
        'Correios', 'FixedAmount', 'Free', 'WithoutShippingPickUp', 'WithoutShipping'
    );

    /**
     * The list of valid Recurrent Interval Types
     *
     * @var array
     */
    private $validRecurrentIntervalTypes = array(
        'Monthly', 'Bimonthly', 'Quarterly', 'SemiAnnual', 'Annual'
    );

    /**
     * The constructor basically
     * loads the configuration
     *
     * Any other action must be executed from
     * The object instance
     */
    public function __construct() {

        $this->loadMerchantInformation();
    }

    /**
     * Private action for loading the JSON
     * configuration file
     *
     * This must run on this class constructor
     */
    private function loadMerchantInformation() {

        $configFile = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/classes/' . self::CNFG_FILE;

        if (!is_file($configFile)) {
            $this->errors[] = 'Merchant Configuration File Missing';
            return;
        }

        $config     = file_get_contents($configFile);
        $configData = json_decode($config, true);

        if (!$configData) {
            $this->errors[] = 'Merchant Configuration File Corrupted';
            return;
        }

        $requiredInfo = array(
            'merchantId', 'checkoutUrl'
        );

        array_walk($configData, function($item, $key) use ($requiredInfo) {

            !in_array($key, $requiredInfo) ||
                $this->$key = $item;
        });

    }

    /**
     * Generic Type Validator
     *
     * this will look for an array in a private property with
     * the syntax valid[YOUR_TYPE]Types
     *
     * The string in the variable "$value" must exist
     * inside this array, otherwise it will generate an error (Logging, Not throwing)
     *
     * @param   string      $value      - The type value to look for
     * @param   string      $info       - The haystack name to find the property as "valid[$info]Types"
     * @return  string
     */
    private function validateType($value, $info) {

        if (!property_exists($this, 'valid' . $info . 'Types')) {

            $this->errors[] = 'There is no validation information for ' . $info;
            return '';
        }

        $validTypes = $this->{'valid' . $info . 'Types'};

        if (!in_array($value, $validTypes)) {

            $this->errors[] = $info . ' type ' . $value . ' is invalid';
            return '';
        }

        return $value;

    }

    /**
     * Generic Date Validator
     *
     * this will try to convert the value to the
     * Y-m-d format, generating an error if it fails (Logging, Not throwing)
     *
     * @param   string|DateTime     $value      - The original date
     * @return  bool|string
     */
    private function validateDate($value) {

        is_a($value, 'DateTime') ||
            $value = strtotime($value);

        $value = date('Y-m-d', $value);

        if (!$value)
            $this->errors[] = 'Date ' . $value . ' is not a valid date';

        return $value;

    }

    /**
     * Generic E-mail validator
     *
     * The original text remains unchanged
     * this will only generate an error
     * in case the validation fails (Logging, Not throwing)
     *
     * @param   string      $email      - The e-mail to validate
     * @return  string
     */
    private function validateEmail($email) {

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $this->errors[] = 'E-mail "'. $email .'" is not a valid e-mail';

        return $email;
    }

    /**
     * Generic validator for Numbers
     *
     * The value must contain digits only,
     * otherwise an error will be generated (Logging, Not Throwing)
     *
     * This function allows leading zeroes
     *
     * @param   string          $number     - The string number
     * @param   bool|string     $length     - The manx length
     * @return  string
     */
    private function validateNumber($number, $length = false) {

        $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);

        if ($length && strlen($number) > $length)
            $this->errors[] = 'Number is too large for field: "' . $number . '"';

        if (!ctype_digit($number))
            $this->errors[] = 'The number ' . $number . ' should contain only digits';

        return $number;
    }

    /**
     * Generic validator for strings
     *
     * This will sanitize string quotes and
     * may also check for string length,
     * generating an error if it's too large (Logging, Not Throwing)
     *
     * @param   string          $string     - The original string
     * @param   bool|int        $length       - The max length
     * @return  mixed
     */
    private function validateString($string, $length = false) {

        if ($length && strlen($string) > $length)
            $this->errors[] = 'String is too large for field: "' . $string . '"';

        return filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    /**
     * Returns checkout URL
     *
     * @return string
     */
    public function getUrl() {

        return $this->checkoutUrl;
    }

    /**
     * Returns the response content
     *
     * @return array
     */
    public function getResponse() {

        return $this->response;
    }

    /**
     * Returns the json data that
     * will be sent as body on the checkout request
     *
     * @return array
     */
    public function getJsonData() {

        return $this->jsonData;
    }

    /**
     * Sets the Order Number
     * to be sent on body of the request
     *
     * @param   int     $number     - The Order Number
     */
    public function setOrderNumber($number) {

        $this->jsonData['OrderNumber'] = $this->validateNumber($number, 64);
    }

    /**
     * The name that will appear in the billing information
     *
     * @param   string      $name       - The Descriptor Name
     */
    public function setBillingName($name) {

        $this->jsonData['SoftDescriptor'] = $this->validateString($name, 13);
    }

    /**
     * Adds an Item to the body
     *
     * @param   string      $name               - The Item Name
     * @param   string      $description        - The Item Description
     * @param   int         $unitPrice          - The price per unit
     * @param   int         $quantity           - Item Quantity
     * @param   int         $weight             - Weight per unit
     * @param   string      $type               - The Item Type [ Asset | Digital | Service | Payment ]
     * @param   string      $sku                - I have no idea what it does, I recommend you to leave blank
     */
    public function addItem($name, $description, $unitPrice, $quantity, $weight = 0, $type = 'Asset', $sku = '') {

        $this->jsonData['Cart']['Items'][] = array(
            'Name'          => $name,
            'Description'   => $description,
            'UnitPrice'     => $this->validateNumber($unitPrice, 18),
            'Quantity'      => $this->validateNumber($quantity, 9),
            'Weight'        => $this->validateNumber($weight, 9),
            'Type'          => $this->validateType($type, 'Item'),
            'Sku'           => $this->validateString($sku, 32)     // Whatever the hell it is
        );

    }

    /**
     * The Order Discount
     *
     * @param   int         $value      - The discount value
     * @param   string      $type       - The discount type [ Percent | Amount ]
     */
    public function setDiscount($value, $type = 'Percent') {

        $this->jsonData['Cart']['Discount'] = array(
            'Type'      => $this->validateType($type, 'Discount'),
            'Value'     => $this->validateNumber($value, 18)
        );

    }

    /**
     * Sets the Shipping Information
     *
     * @param   int     $sourceZip      - The Source Zip Code (only Numbers)
     * @param   int     $destZip        - The Target Zip Code (only Numbers)
     * @param   string  $street         - The Street Address
     * @param   string  $number         - The Street Number ( Accepts alphanumeric values )
     * @param   string  $complement     - The Address Complement
     * @param   string  $district       - The Address District
     * @param   string  $city           - The Address City
     * @param   string  $state          - The Address State
     * @param   string  $type           - The Shipping Type [ Correios | FixedAmount | Free | WithoutShippingPickUp | WithoutShipping ]
     */
    public function setShippingInformation($sourceZip, $destZip, $street, $number, $complement = '', $district = '', $city = '', $state = '', $type = 'Correios') {

        $this->validateType($type, 'Shipping');

        $this->jsonData['Shipping'] = array(
            'Type'          => $type,
            'SourceZipCode' => $this->validateNumber($sourceZip),
            'TargetZipCode' => $this->validateNumber($destZip),
            'Address'       => array(
                'Street'        => $this->validateString($street, 256),
                'Number'        => $this->validateNumber($number, 8),
                'Complement'    => $this->validateString($complement, 256),
                'District'      => $this->validateString($district, 64),
                'City'          => $this->validateString($city, 64),
                'State'         => $this->validateString($state, 2)
            )
        );

    }

    /**
     * Adds a shipping service to the list of services
     *
     * @param   string      $name           - The service name
     * @param   string      $price          - The service price
     * @param   string      $deadline       - The deadline ( days )
     */
    public function addShippingService($name, $price, $deadline) {

        $this->jsonData['Shipping']['Services'][] = array(
            'Name'      => $this->validateString($name, 128),
            'Price'     => $this->validateNumber($price, 18),
            'DeadLine'  => $this->validateNumber($deadline, 9)
        );

    }

    /**
     * Sets the payment information
     *
     * @param   bool|string             $recurrentInterval      - The recurrent interval [ bool:false (not recurrent) | Monthly | Bimonthly | Quarterly | SemiAnnual | Annual ]
     * @param   bool|string|DateTime    $endDate                - The recurrent end date ( if recurrent )
     * @param   int                     $billDiscount           - The discount if bill payment
     * @param   int                     $debitDiscount          - The discount if debit payment
     */
    public function setPaymentType($recurrentInterval = false, $endDate = false, $billDiscount = 0, $debitDiscount = 0) {

        $this->jsonData['Payment'] = array(
            'BoletoDiscount'    => $this->validateNumber($billDiscount, 3),
            'DebitDiscount'     => $this->validateNumber($debitDiscount, 3)
        );

        if (!$recurrentInterval) return;

        $this->jsonData['Payment']['RecurrentPayment'] = array(
            'Interval'  => $this->validateType($recurrentInterval, 'RecurrentInterval'),
            'EndDate'   => $this->validateDate($endDate)
        );

    }

    /**
     * Sets the customer information
     *
     * @param   string      $name           - The customer full name
     * @param   string      $identity       - The customer CPF or CNPJ
     * @param   string      $email          - The customer e-mail
     * @param   string      $phone          - The customer phone
     */
    public function setCustomer($name, $identity, $email = '', $phone = '') {

        $this->jsonData['Customer'] = array(
            'Identity'  => $this->validateNumber($identity, 14),
            'FullName'  => $this->validateString($name, 288),
            'Email'     => $this->validateEmail($email),
            'Phone'     => $this->validateNumber($phone, 11)
        );
    }

    /**
     * Returns the necessary Cielo HTTP Headers
     *
     * @return array
     */
    private function getCheckoutHeaders() {

        return array(
            'MerchantId:'   . $this->merchantId,
            'Content-type:' . 'application/json'
        );
    }

    /**
     * Performs a Checkout Validation,
     * identifying possible missing required values
     * and other types of validations
     *
     * @return bool
     */
    private function validateCheckout() {

        if (!filter_var($this->checkoutUrl, FILTER_VALIDATE_URL))
            $this->errors[] = 'The configuration URL in configuration file is malformed or missing: "' . $this->checkoutUrl . '"';

        if (empty($this->merchantId) ||
                strlen($this->merchantId) < 36)
                $this->errors[] = 'The configured Merchant ID in configuration file is incorrect or missing: "'. $this->merchantId .'"';

        //TODO: validate required fields


        if (count($this->errors) > 0) return false;

        return true;
    }

    /**
     * Returns the client side errors
     * in the same structure of the server
     * error response so it can be easily parsed
     *
     * @return  array
     */
    private function returnErrors() {

        return array(
            'Settings' => array(
                'type'      => 'Client Side Errors',
                'message'   => $this->errors
            )
        );
    }

    /**
     * Performs the checkout request
     *
     * @return array|mixed|string
     */
    public function checkout() {

        if (!$this->validateCheckout()) {
            $this->response = $this->returnErrors();
            return $this->response;
        }

        $ch = curl_init($this->checkoutUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCheckoutHeaders());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_SSLVERSION , 1);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->jsonData));

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response   = curl_exec($ch);
        $this->info = curl_getinfo($ch);

        $this->response = json_decode($response, true);
        if (!$this->response) $this->response = curl_error($ch);

        return $this->response;

    }

}