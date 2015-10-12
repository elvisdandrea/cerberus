<?php
/**
 * Class        HttpHandler
 * @package     Rest\RestBundle\Handler
 * @author      Elvis D'Andrea
 * @email       elvis@vistasoft.com.br
 *
 *
 *
 */
class HttpHandler {

    /**
     * The Request URL
     *
     * @var string
     */
    private $url = '';

    /**
     * The Request Method
     *
     * ( GET | POST | PUT | DELETE | UPDATE | PATCH )
     * - Default = GET
     *
     * @var string
     */
    private $method = 'GET';

    /**
     * Request Parameters andd Values
     *
     * @var array
     */
    private $params = array();

    /**
     * Request Headers
     *
     * @var array
     */
    private $headers = array();

    /**
     * Request Errors
     *
     * @var array
     */
    private $errors = array();

    /**
     * The Response Content
     *
     * @var
     */
    private $content;

    private $useSSL = false;

    /**
     * The Constructor
     *
     * @param   string      $url    - The Request URL
     */
    public function __construct($url = '') {
        $this->url = $url;
    }

    /**
     * An static call to create an instance
     *
     * @param   string          $url        - The Request URL
     * @param   string          $method     - The Request Method ( GET | POST | PUT | DELETE | UPDATE | PATCH )
     * @return  HttpHandler
     */
    public static function Create($url, $method) {

        $http = new HttpHandler($url);
        $http->setMethod($method);
        return $http;
    }

    /**
     * Defines the Request Method
     *
     * @param   string      $method     - ( GET | POST | PUT | DELETE | UPDATE | PATCH )
     */
    public function setMethod($method) {
        $validate = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'UPDATE');
        if (!in_array(strtoupper($method), $validate))
            $this->errors['EXTERNAL_REQUEST_INVALID_METHOD'] = 'Desculpe, uma execução do servidor não foi correta. Contate o suporte da Vista e informe EXTERNAL_REQUEST_INVALID_METHOD';

        $this->method = strtoupper($method);
    }

    /**
     * Adds a Request Body Param
     *
     * @param   string      $param      - The param name
     * @param   mixed       $value      - The param value
     */
    public function addParam($param, $value) {

        $this->params[$param] = $value;
    }

    /**
     * Adds a Header to the Request
     *
     * @param   string      $name       - The Header name
     * @param   string      $value      - The Header value
     */
    public function addHeader($name, $value) {

        $this->headers[$name] = $value;
    }

    public function clearParams(){
        $this->params = array();
    }

    /**
     * Defines the Request URL
     *
     * @param   string      $url        - The Request URL
     */
    public function setURL($url) {

        $this->url = $url;
    }

    /**
     * Stores all errors that may happen
     * along the process
     *
     * @return  array       - The error list
     */
    public function getErrors() {

        return $this->errors;
    }

    /**
     * Builds the Header Array to be used
     * in cURL request
     *
     * @return array
     */
    private function buildHeaders() {

        $headers = array();
        foreach ($this->headers as $header => $value)
            $headers[] = $header . ': ' . $value;
        return $headers;
    }

    /**
     * Returns the Response Content after
     * the Request Execution
     *
     * @return  mixed
     */
    public function getContent() {

        return $this->content;
    }

    /**
     * True|False if an
     * error has occurred so far
     *
     * @return bool
     */
    public function hasErrors() {

        return count($this->errors) > 0;
    }

    public function setSSL($bool){
        $this->useSSL = $bool;
    }

    /**
     * Executes the Request
     */
    public function execute() {

        $url = $this->url;

        if ($this->method == 'GET')
            $url .= '?' . http_build_query($this->params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 Loximi sfFacebookPhoto PHP5 (cURL)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if($this->useSSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        switch ($this->method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->params));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
                break;
            case 'UPDATE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'UPDATE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
                break;
            default:
                break;
        }

        if (count($this->headers) > 0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders());

        $this->content = curl_exec($ch);

        if (!$this->content)
            $this->errors['EXTERNAL_REQUEST_EXECUTE_ERROR'] = curl_error($ch);

        curl_close($ch);


    }


}