<?php

/**
 * Class CerberusClient
 *
 * Client for Cerberus RESTful API
 */
class CerberusClient {

    /**
     * Authentication Id
     *
     * @var
     */
    private $id;

    /**
     * Authentication Secret Word
     *
     * @var
     */
    private $secret;

    /**
     * Authentication
     *
     * @var
     */
    private $authentication;

    /**
     * API URL
     *
     * @var string
     */
    private $url;

    /**
     * Handler for HTTP Client
     *
     * @var HttpHandler
     */
    private $request;

    public function __construct() {

        $this->url      = 'http://api.orbit.gravi.com.br/';
        $this->request  = new HttpHandler($this->url);
        $this->request->addHeader('accept', 'application/json');
        $this->LoadApi();
    }

    /**
     * Loads the API Authentication and
     * check if token is not expired
     */
    private function LoadApi() {

        $this->authentication = Session::get('authentication');

        if (!isset($this->authentication['access_token'])) $this->apiLogin();

        $expires = new DateTime($this->getTokenExpire());
        $now     = new DateTime();

        if ($now > $expires) $this->apiLogin();

        if (!$this->checkTokenAlive()) $this->apiLogin();

    }

    /**
     * Performs Login in the Orbit API
     */
    private function apiLogin() {

        $this->loadAthentication();

        $this->request->setURI('apilogin');

        $this->request->addParam('id',     $this->id);
        $this->request->addParam('secret', $this->secret);
        $this->request->execute();

        $result = json_decode($this->request->getContent(), true);

        if (isset($result['access_token'])) {
            $this->authentication = $result;
            Session::set('authentication', $this->authentication);
        }

    }

    /**
     * Loads the API Authentication File with
     * encrypted code
     */
    private function loadAthentication() {

        $authFile = IFCDIR . '/data/' . md5(ORBIT_ID);
        if (!file_exists($authFile)) return;

        $content = file_get_contents($authFile);
        $content = CR::decrypt($content);
        $data    = json_decode($content, true);

        if (!$data) return;

        foreach (array('id', 'secret') as $item)
            if (!isset($data[$item])) return;
            else $this->$item = $data[$item];

    }

    /**
     * Checks if Token is alive on server
     */
    private function checkTokenAlive() {

        $this->request->clearParams();
        $this->request->setMethod('get');
        $this->request->setURI('auth/tokenisalive');
        $this->request->addParam('token', $this->getToken());

        $this->request->execute();
        $result = $this->request->getContent();

        return $result['status'] == 200;
    }

    public function getToken() {

        if (isset($this->authentication['access_token']))
            return $this->authentication['access_token'];

        return false;
    }

    /**
     * Returns the Expiration Date of the Token
     *
     * @return bool
     */
    public function getTokenExpire() {

        if (isset($this->authentication['expires']))
            return $this->authentication['expires'];

        return false;
    }

    /**
     * Returns the User Authentication Id
     *
     * @return bool
     */
    public function getUid() {

        if (isset($this->authentication['uid']))
            return $this->authentication['uid'];

        return false;
    }

    /**
     * Returns the Company ID
     *
     * @return bool
     */
    public function getCompanyId() {

        if (isset($this->authentication['company_id']))
            return $this->authentication['company_id'];

        return false;
    }

    /**
     * Returns the URL of the last request performed to the API
     *
     * @return string
     */
    public function getURL() {

        return $this->request->getURL();
    }


    /**
     * Executes a method on Cerberus API and get the response
     *
     * @param   string      $method     - The API method
     * @param   int         $page       - Number of Current Result Page (for paginated methods)
     * @param   int         $rp         - Number of Results per Page
     * @param   bool|array  $filters    - Filters of the method
     * @param   bool|array  $order      - Result order
     * @return  mixed
     */
    public function get($method, $page = 1, $rp = 10, $filters = false, $order = false) {

        $this->request->clearParams();
        $this->request->setMethod('get');
        $this->request->setURI($method);
        $this->request->addParam('token', $this->getToken());

        foreach (array('filters', 'order') as $operation) {
            if ($$operation) {
                foreach ($$operation as $opField => $opValue) {
                    $this->request->addParam($opField, $opValue);
                }
            }
        }

        $this->request->addParam('page', $page);
        $this->request->addParam('rp',   $rp);
        $this->request->execute();

        $content = json_decode($this->request->getContent(), true);
        return $content;
    }

    /**
     * Executes a post method to the Cerberus API
     *
     * @param   string      $method
     * @param   array       $data
     * @return  mixed
     */
    public function post($method, $data) {

        $this->request->clearParams();
        $this->request->setMethod('post');
        $this->request->setURI($method . '?token=' . $this->getToken());
        foreach ($data as $param => $value) $this->request->addParam($param, $value);
        $this->request->execute();

        $content = json_decode($this->request->getContent(), true);
        return $content;
    }

    /**
     * Executes a put method to the Cerberus API
     *
     * @param $method
     * @param $data
     * @return mixed
     */
    public function put($method, $data) {

        $this->request->clearParams();
        $this->request->setMethod('put');
        $this->request->setURI($method . '?token=' . $this->getToken());
        foreach ($data as $param => $value) $this->request->addParam($param, $value);
        $this->request->execute();

        $content = json_decode($this->request->getContent(), true);
        return $content;
    }


}