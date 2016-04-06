<?php

/**
 * Class CieloClient
 *
 * This class generates the body of the request for
 * integrating with Cielo Webservice 3 API
 *
 * Legacy Support for version 1.5
 *
 * @author  Elvis D'Andrea
 * @email   elvis.gravi@gmail.com
 */
class CieloClient {

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
     * The json with the information for
     * accessing the Legacy 1.5 API
     */
    const CNFG_FILE_LEGACY  = 'conf/cielo.conf-1.5.json';

    /**
     * The Cielo Merchant ID
     *
     * @var string
     */
    private $merchantId     = '';

    /**
     * The Cielo Merchant Key
     *
     * @var string
     */
    private $merchantKey    = '';

    /**
     * The Cielo Merchant ID for test environment
     *
     * @var string
     */
    private $sandboxId      = '';

    /**
     * The Cielo Merchant ID for test environment
     *
     * @var string
     */
    private $sandboxKey     = '';

    /**
     * The Cielo Checkout API URL
     *
     * @var string
     */
    private $apiUrl      = '';

    /**
     * URL of the test environment
     *
     * @var string
     */
    private $sandboxUrl  = '';

    /**
     * If it must use test environment
     *
     * @var bool
     */
    private $sandbox     = false;

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
        'MerchantOrderId'   => '',
        'Customer'          => array(),
        'Payment'           => array()
    );

    /**
     * List of required data
     *
     * @var array
     */
    private $requirements = array(
        'MerchantOrderId'   => '',
        'Customer'          => array(),
        'Payment'           => array(),
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
     * The list of valid Payment Types
     *
     * @var array
     */
    private $validPaymentTypes = array(
        'CreditCard', 'DebitCard', 'Boleto'
    );

    /**
     * List of valid Brand Types
     *
     * @var array
     */
    private $validBrandTypes    = array(
        'Visa', 'Master', 'MasterCard', 'Amex', 'Elo', 'Auria', 'JCB', 'Diners', 'Discover'
    );

    /**
     * List of valid Interval Types
     *
     * @var array
     */
    private $validIntervalTypes = array(
        'Monthly', 'Bimonthly', 'Quarterly', 'SemiAnnual', 'Annual'
    );

    /**
     * Which API version to use
     *
     * @var string
     */
    private $apiVersion    = '3';

    /**
     * Current transaction id (API version 1.5)
     *
     * @var string
     */
    private $transactionId = '';

    /**
     * The constructor basically
     * loads the configuration
     *
     * Any other action must be executed from
     * The object instance
     */
    public function __construct($sandbox = false, $apiVersion = '3') {

        $this->sandbox    = $sandbox;
        $this->apiVersion = $apiVersion;
        $this->loadMerchantInformation();
    }

    /**
     * Private action for loading the JSON
     * configuration file
     *
     * This must run on this class constructor
     */
    private function loadMerchantInformation() {

        $configFile = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/classes/' . ($this->apiVersion == '1.5' ? self::CNFG_FILE_LEGACY : self::CNFG_FILE);

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
            'merchantId', 'merchantKey', 'sandboxId', 'sandboxKey', 'apiUrl', 'sandboxUrl'
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
            $this->errors[] = 'Date "' . $value . '" is not a valid date';

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
     * @param   bool|int    $length     - The max length
     * @return  string
     */
    private function validateEmail($email, $length = false) {

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $this->errors[] = 'E-mail "'. $email .'" is not a valid e-mail';

        if ($length && strlen($email) > $length)
            $this->errors[] = 'String too large for E-mail "'. $email .'"';

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

        if (empty($number)) return $number;

        $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
        $number = str_replace(array('+','-'), '', $number);

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

        return $this->apiUrl;
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
     * Returns the request information
     *
     * @return array
     */
    public function getRequestInfo() {

        return $this->info;
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
     * @param   int     $orderId     - The Order Number
     */
    public function setOrderId($orderId) {

        $this->jsonData['MerchantOrderId'] = $this->validateNumber($orderId, 64);
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
     * Sets the customer information
     *
     * @param   string      $name           - The customer full name
     * @param   string      $email          - The customer e-mail
     * @param   string      $birthdate      - The customer birthdate
     */
    public function setCustomer($name, $email = '', $birthdate = '') {

        $address = false;

        if (isset($this->jsonData['Customer']['Address']))
            $address = $this->jsonData['Customer']['Address'];

        $this->jsonData['Customer'] = array(
            'Name'      => $this->validateString($name, 288),
            'Email'     => $this->validateEmail($email, 255),
            'Birthdate' => $this->validateDate($birthdate)
        );

        if ($address)
            $this->jsonData['Customer']['Address'] = $address;
    }

    /**
     * Sets the Customer Address
     *
     * @param   string      $street         - The Street Address
     * @param   string      $number         - The Street Number
     * @param   string      $complement     - The Address Complement
     * @param   string      $zipCode        - The Zip Code
     * @param   string      $city           - The Address City
     * @param   string      $state          - The Address State
     * @param   string      $country        - The Address Country
     */
    public function setCustomerAddress($street, $number, $complement, $zipCode, $city, $state, $country = 'BRA') {

        $this->jsonData['Customer']['Address'] = array(
            'Street'        => $this->validateString($street, 255),
            'Number'        => $this->validateNumber($number, 15),
            'Complement'    => $this->validateString($complement, 50),
            'ZipCode'       => $this->validateNumber($zipCode, 9),
            'City'          => $this->validateString($city, 50),
            'State'         => $this->validateString($state, 2),
            'Country'       => $this->validateString($country, 35)
        );
    }

    /**
     * Sets the Delivery Address
     *
     * @param   string      $street         - The Street Address
     * @param   string      $number         - The Street Number
     * @param   string      $complement     - The Address Complement
     * @param   string      $zipCode        - The Zip Code
     * @param   string      $city           - The Address City
     * @param   string      $state          - The Address State
     * @param   string      $country        - The Address Country
     */
    public function setDeliveryAddress($street, $number, $complement, $zipCode, $city, $state, $country = 'BRA') {

        $this->jsonData['Customer']['DeliveryAddress'] = array(
            'Street'        => $this->validateString($street, 255),
            'Number'        => $this->validateNumber($number, 15),
            'Complement'    => $this->validateString($complement, 50),
            'ZipCode'       => $this->validateNumber($zipCode, 9),
            'City'          => $this->validateString($city, 50),
            'State'         => $this->validateString($state, 2),
            'Country'       => $this->validateString($country, 35)
        );
    }

    /**
     * Sets the payment information
     *
     * @param   string      $type                   - The Payment type [ CreditCard | DebitCard | Boleto ]
     * @param   string      $amount                 - The Payment Amount ( in cents )
     * @param   int         $installments           - Number of Installments
     * @param   string      $provider               - The Payment Provider ( E.g.: Bradesco )
     * @param   string      $currency               - The Payment Currency
     * @param   string      $country                - The Payment Country
     * @param   int         $serviceTaxAmount       - The Tax for the transaction
     * @param   string      $interest               - Parcel Type ( E.g.: ByMerchant )
     */
    public function setPaymentInfo($type, $amount, $installments = 1, $provider = '', $currency = 'BRL', $country = 'BRA', $serviceTaxAmount = 0, $interest = 'ByMerchant') {

        $this->jsonData['Payment'] = array(
            'Type'              => $this->validateType($type, 'Payment'),
            'Amount'            => $this->validateNumber($amount, 15),
            'Currency'          => $this->validateString($currency, 3),
            'Country'           => $this->validateString($country, 35),
            'Provider'          => $this->validateString($provider, 15),
            'ServiceTaxAmount'  => $this->validateNumber($serviceTaxAmount, 15),
            'Installments'      => $this->validateNumber($installments, 15),
            'Interest'          => $this->validateString($interest, 10),
            'Capture'           => false,
            'Authenticate'      => false
        );

    }

    /**
     * Sets the Recurrent Payment Information
     *
     * @param   string              $interval           - The recurrent payment interval [ Monthly | Bimonthly | Quarterly | SemiAnnual | Annual ]
     * @param   string|DateTime     $startDate          - The recurrent payment start date
     * @param   string|DateTime     $endDate            - The recurrent payment end date
     * @param   bool                $authorizeNow       - If the first recurrent payment will be already authorized
     */
    public function setRecurrentPaymentInfo($interval, $startDate, $endDate, $authorizeNow = true) {

        $this->jsonData['Payment']['RecurrentPayment'] = array(
            'AuthorizeNow'      => $authorizeNow,
            'StartDate'         => $this->validateDate($startDate),
            'EndDate'           => $this->validateDate($endDate),
            'Interval'          => $this->validateType($interval, 'Interval')
        );
    }

    /**
     * Sets the credit card information
     *
     * @param   string              $cardNumber         - The Credit Card Number
     * @param   string              $holderName         - The Holder Name
     * @param   string              $expirationDate     - The Expiration Date
     * @param   string              $securityCode       - The Security Code on the back of the card
     * @param   string              $brand              - The Brand Name [ Visa | Master | Amex | Elo | Auria | JCB | Diners | Discover ]
     * @param   bool                $saveCard
     */
    public function setCreditCardInfo($cardNumber, $holderName, $expirationDate, $securityCode, $brand, $saveCard = false) {

        $this->jsonData['Payment']['CreditCard'] = array(
            'CardNumber'        => $this->validateNumber($cardNumber, 16),
            'Holder'            => $this->validateString($holderName, 25),
            'ExpirationDate'    => $this->validateString($expirationDate, 7),
            'SecurityCode'      => $this->validateNumber($securityCode, 4),
            'Brand'             => $this->validateType($brand, 'Brand'),
            'SaveCard'          => $saveCard
        );
    }

    /**
     * Sets the debit card information
     *
     * @param   string              $cardNumber         - The Debit Card Number
     * @param   string              $holderName         - The Holder Name
     * @param   string              $expirationDate     - The Expiration Date
     * @param   string              $securityCode       - The Security Code on the back of the card
     * @param   string              $brand              - The Brand Name [ Visa | Mastercard | Amex | Elo | Auria | JCB | Diners | Discover ]
     * @param   bool                $saveCard
     */
    public function setDebitCardInfo($cardNumber, $holderName, $expirationDate, $securityCode, $brand, $saveCard = false) {

        $this->jsonData['Payment']['DebitCard'] = array(
            'CardNumber'        => $this->validateNumber($cardNumber, 16),
            'Holder'            => $this->validateString($holderName, 25),
            'ExpirationDate'    => $this->validateString($expirationDate, 7),
            'SecurityCode'      => $this->validateNumber($securityCode, 4),
            'Brand'             => $this->validateType($brand, 'Brand'),
            'SaveCard'          => $saveCard
        );
    }

    /**
     * Returns the necessary Cielo HTTP Headers
     *
     * @return array
     */
    private function getHeaders() {

        $contentType = $this->apiVersion == '1.5' ? 'application/x-www-form-urlencoded' : 'application/json';

        switch ($this->apiVersion) {
            case '1.5':
                return array(
                    'Content-type:' . $contentType
                );
                break;
            case '3':
            default :
                return array(
                    'MerchantId:'   . $this->merchantId,
                    'MerchantKey:'  . $this->merchantKey,
                    'Content-type:' . 'application/json'
                );
                break;
        }
    }

    /**
     * Performs a Checkout Validation,
     * identifying possible missing required values
     * and other types of validations
     *
     * @return bool
     */
    private function validateCheckout() {

        if (!filter_var($this->apiUrl, FILTER_VALIDATE_URL))
            $this->errors[] = 'The configuration URL in configuration file is malformed or missing: "' . $this->apiUrl . '"';

        if (empty($this->merchantId) ||
            (strlen($this->merchantId) < 36 && $this->apiVersion == '3') )
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
     * Calls the request with sales method
     *
     * @return array|mixed|string
     */
    public function processPayment() {

        $method = $this->apiVersion == '1.5' ? '' : '/1/sales';
        return $this->request($method);
    }

    /**
     * Generates the Data according to API Version
     *
     * @return string
     */
    private function generateData() {

        switch ($this->apiVersion) {
            case '1.5':
                return http_build_query(array('mensagem' => $this->apiXmlData()));
                break;
            case '3' :
                return json_encode($this->jsonData);
                break;
            default :
                return json_encode($this->jsonData);
                break;
        }
    }

    /**
     * Performs the request
     *
     * @param   string      $method     - Which method will be executed
     * @return  array|mixed|string
     */
    private function request($method) {

        if (!$this->validateCheckout()) {
            $this->response = $this->returnErrors();
            return $this->response;
        }

        $url = $this->sandbox ? $this->sandboxUrl : $this->apiUrl;

        $ch = curl_init($url . $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
        curl_setopt($ch, CURLOPT_TIMEOUT , 60);
        curl_setopt($ch, CURLOPT_SSLVERSION , 1);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->generateData());

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response   = curl_exec($ch);
        $this->info = curl_getinfo($ch);

        file_put_contents('../classes/logs/transfer.log', $response, FILE_APPEND);

//        print_r($response); exit;

        $this->processResponse($response);
        if (!$this->response) $this->response = curl_error($ch);

        return $this->response;

    }

    /**
     * Process API response to Array
     * according to API version
     *
     * @param $response
     */
    private function processResponse($response) {

        switch ($this->apiVersion) {
            case '1.5':
                $xml      = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                $json     = json_encode($xml);
                $result   = json_decode($json, true);
                $cardType = isset($this->jsonData['Payment']['DebitCard']) ? 'DebitCard' : 'CreditCard';

                $this->response = array(
                    'Payment' => array(
                        'ServiceTaxAmount'  => $result['dados-pedido']['taxa-embarque'],
                        'Interest'          => 'ByMerchant',
                        'CreditCard'        => array(
                            'CardNumber'        => $this->jsonData['Payment'][$cardType]['CardNumber'],
                            'Holder'            => $this->jsonData['Payment'][$cardType]['Holder'],
                            'Brand'             => $this->jsonData['Payment'][$cardType]['Brand']
                        ),
                        'Tid'               => $result['tid'],
                        'PaymentId'         => $result['@attributes']['id'],
                        'Provider'          => '',
                        'Type'              => $cardType,
                        'Amount'            => $result['autorizacao']['valor'],
                        'ReceivedDate'      => date('Y-m-d'),
                        'Currency'          => $this->jsonData['Payment']['Currency'],
                        'Country'           => '',
                        'ReturnCode'        => $result['autorizacao']['codigo'],
                        'ReturnMessage'     => $result['autorizacao']['mensagem']
                    )
                );

                if (isset($result['token'])) {
                    $this->response['Payment']['RecurrentPayment']['RecurrentPaymentId'] = $result['token']['dados-token']['codigo-token'];
                    $this->response['Payment']['CreditCard']['CardNumber'] = $result['token']['dados-token']['numero-cartao-truncado'];
                }
                break;
            case '3':
                $this->response = json_decode($response, true);
                break;
            default:
                $this->response = json_decode($response, true);
                break;
        }
    }


    /**
     * Generates the legacy API XML (API version 1.5)
     *
     * @return string
     */
    private function apiXmlData() {

        $cardType =  isset($this->jsonData['Payment']['DebitCard']) ? 'DebitCard' : 'CreditCard';
        $product  = '1';

        if ($cardType == 'CreditCard') {
            if ($this->jsonData['Payment']['Installments'] > 1) $product = '2';
        } else {
            $product = 'A';
        }

        $authorize     = '3';
        $generateToken = isset($this->jsonData['Payment']['RecurrentPayment']) ? '<gerar-token>true</gerar-token>' : '';

        $cardValidDate = explode('/', $this->jsonData['Payment'][$cardType]['ExpirationDate']);
        $cardValidDate = $cardValidDate[1] . $cardValidDate[0];

        $currencies = array(
            'BRL'   => 986
        );

        $paymentCurrency =  $this->jsonData['Payment']['Currency'];
        $currency = isset($currencies[$paymentCurrency]) ? $currencies[$paymentCurrency] : 986;

        $this->transactionId = md5(date("YmdHisu"));

        /**
         * Unfortunately, this is the simplest way to generate the request body
         */
        $xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
                <requisicao-transacao id="' . $this->transactionId . '" versao="1.2.1">
                <dados-ec>
                    <numero>' . ($this->sandbox ? $this->sandboxId  : $this->merchantId)  . '</numero>
                    <chave>'  . ($this->sandbox ? $this->sandboxKey : $this->merchantKey) . '</chave>
                </dados-ec>
                <dados-portador>
                    <numero>' . $this->jsonData['Payment'][$cardType]['CardNumber'] . '</numero>
                    <validade>' . $cardValidDate . '</validade>
                    <indicador>1</indicador>
                    <codigo-seguranca>' . $this->jsonData['Payment'][$cardType]['SecurityCode'] . '</codigo-seguranca>
                    <nome-portador>' . $this->jsonData['Payment'][$cardType]['Holder'] . '</nome-portador>
                </dados-portador>
                <dados-pedido>
                    <numero>' . $this->jsonData['MerchantOrderId'] . '</numero>
                    <valor>' . $this->jsonData['Payment']['Amount'] . '</valor>
                    <moeda>' . $currency . '</moeda>
                    <data-hora>' . date('Y-m-d') . 'T' . date('h:i:s') . '</data-hora>
                </dados-pedido>
                <forma-pagamento>
                    <bandeira>' . strtolower($this->jsonData['Payment'][$cardType]['Brand']) . '</bandeira>
                    <produto>'  . $product . '</produto>
                    <parcelas>' . $this->jsonData['Payment']['Installments'] . '</parcelas>
                </forma-pagamento>
                <url-retorno>null</url-retorno>
                <autorizar>' . $authorize . '</autorizar>
                <capturar>false</capturar>
                '. $generateToken .'
                </requisicao-transacao>';

        return $xml;
    }

}