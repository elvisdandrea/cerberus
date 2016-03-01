<?php

/**
 * Class Notifications
 *
 * Push Notifications class for
 * Mobile Devices
 *
 * @author  Elvis D'Andrea
 * @email   elvis.gravi@gmail.com
 */
class Notifications {

    /**
     * JSON Configuration file
     * for production environment
     */
    const CNFG_FILE    = 'config/notifications.config.json';

    /**
     * JSON Configuration file
     * for test environment
     */
    const CNFG_SANDBOX = 'config/notifications.sandbox.json';

    /**
     * List of devices ids separated
     * by its platform
     *
     * @var array
     */
    private $pushList = array();

    /**
     * The notification content
     *
     * @var array
     */
    private $pushData = array();

    /**
     * The configuration content
     * loaded from file
     *
     * @var array
     */
    private $config   = array();

    /**
     * The responses
     *
     * @var array
     */
    private $result   = array();

    /**
     * List of Runtime errors
     *
     * @var array
     */
    private $errors   = array();

    /**
     * Constructor
     *
     * Loads the configuration file
     *
     * Use TRUE in the parameter to
     * load the test environment configuration
     *
     * @param   bool        $sandbox        - If it must use the test environment
     */
    public function __construct($sandbox = false) {

        $this->loadConfig($sandbox);
    }

    /**
     * Appends to the runtime error list
     *
     * @param   $message        - The error message
     */
    private function appendToErrors($message) {

        $this->errors[] = $message;
    }

    /**
     * Loads the configuration file
     *
     * @param   bool       $sandbox     - If it must use the test environment
     */
    private function loadConfig($sandbox) {

        $configFile = __DIR__ . '/' .  ($sandbox ? self::CNFG_SANDBOX : self::CNFG_FILE);

        if (!file_exists($configFile)) {
            $this->appendToErrors('Configuration file not found: '. $configFile);
            return;
        }

        $content = file_get_contents($configFile);
        $config  = json_decode($content, true);

        if (!$config) {
            $this->appendToErrors('Configuration file is corrupted: '. $configFile);
            return;
        }

        $this->config = $config;

    }


    /**
     * Verifies if the device type is valid
     *
     * The list of valid device types are from
     * the keys of the configuration file
     *
     * @param   string      $deviceType     - The device type ( From configuration file )
     * @return  bool
     */
    private function isValidDeviceType($deviceType) {

        return in_array($deviceType, array_keys($this->config));
    }


    /**
     * Adds a device to push notification list
     *
     * @param   string          $deviceId       - The device Id
     * @param   string          $deviceType     - The device type ( must be valid )
     * @return  array|bool
     */
    public function addDevice($deviceId, $deviceType) {

        if (!$this->isValidDeviceType($deviceType)) {
            $this->appendToErrors('Invalid device type "'. $deviceType .'". Device Id: "'. $deviceId .'"');
            return false;
        }

        return $this->pushList[$deviceType][] = $deviceId;
    }

    /**
     * Creates the data structure for Android Device Type
     *
     * @param   string      $message        - The push message ( false if you send custom information only )
     * @param   array       $customInfo     - Custom data
     */
    private function setPushDataAndroid($message, array $customInfo = array()) {

        $data = array();

        !$message ||
        $data['message'] = $message;

        count($customInfo) == 0 ||
        $data = array_merge($data, $customInfo);

        $this->pushData['android'] = $data;
    }

    /**
     * Creates the data structure for IOS Device Type
     *
     * @param   string          $message        - The push message ( false if you send custom information only )
     * @param   array           $customInfo     - Custom data
     * @param   bool|string     $alert          - Message Alert
     * @param   string          $sound          - Notification sound
     */
    private function setPushDataIos($message, array $customInfo = array(), $alert = false, $sound = 'default') {

        $options = array(
            'sound'             => $sound,
            'content-available' => 1
        );

        !$alert ||
        $options['alert'] = $alert;

        $content = array();

        !$message ||
        $content['message'] = $message;

        count($customInfo) == 0 ||
        $content = array_merge($content, $customInfo);

        $data = array_merge($content, $options);

        $this->pushData['ios'] = array(
            'aps'   => $data
        );

    }

    /**
     * Returns the push result
     *
     * @return array
     */
    public function getResult() {

        return $this->result;
    }

    /**
     * Returns all runtime errors
     *
     * @return array
     */
    public function getErrors() {

        return $this->errors;
    }

    /**
     * Sets the Notification Content
     *
     * @param   string          $message        - The push message ( false if you send custom information only )
     * @param   array           $customInfo     - Custom data
     * @param   bool|string     $alert          - Message Alert
     * @param   string          $sound          - Notification sound
     */
    public function setPushContent($message, array $customInfo = array(), $alert = false, $sound = 'default') {

        $deviceTypes = array_keys($this->config);

        array_walk($deviceTypes, function($type) use ($message, $customInfo, $alert, $sound) {

            $dataMethod = 'setPushData' . $type;
            if (!method_exists($this, $dataMethod)) {
                $this->appendToErrors('There is no data method for device type "'. $type .'"');
                return;
            }

            $this->$dataMethod($message, $customInfo, $alert, $sound);

        });
    }

    /**
     * Notifications execution for Android Devices
     */
    private function pushAndroid() {

        $config = $this->config['android'];

        if (!isset($this->pushList['android']) ||
            count($this->pushList['android']) == 0) return;

        if (!isset($this->pushData['android']) ||
            count($this->pushData['android']) == 0) return;

        $body = array(
            'registration_ids'  => $this->pushList['android'],
            'data'              => $this->pushData['android']
        );

        $http = HttpHandler::Create($config['url'], 'post');
        if ($config['protocol'] == 'https') $http->setSSL(true);

        foreach ($config['options'] as $option => $value)
            $http->addHeader($option, $value);

        $http->addHeader('Authorization', 'key=' . $config['key']);
        $http->setContentType('json');
        $http->setBody($body);

        $http->execute();

        $this->result['android'][] = $http->getContent();

    }

    /**
     * Notifications execution for IOS Devices
     */
    private function pushIos() {

        $config = $this->config['ios'];

        if (!isset($this->pushList['ios']) ||
            count($this->pushList['ios']) == 0) return;

        if (!isset($this->pushData['ios']) ||
            count($this->pushData['ios']) == 0) return;

        $streamContext = stream_context_create();

        foreach ($config['options'] as $option => $value)
            stream_context_set_option($streamContext, $config['protocol'], $option, $value);

        $streamUrl = $config['protocol'] . '://' . $config['url'] . ':' . $config['port'];
        $sock      = stream_socket_client($streamUrl, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

        $payload = json_encode($this->pushData['ios']);

        $l2 = strlen($payload);
        $b2 = $l2 % 256;
        $b1 = ($l2-$b2)/256;
        $l2 = chr($b1) . chr($b2);

        $msg = '';

        foreach ($this->pushList['ios'] as $deviceId) {
            $id = pack('H*', $deviceId);
            $l1 = strlen($id);
            $b2 = $l1 % 256;
            $b1 = ($l1-$b2)/256;
            $l1 = chr($b1) . chr($b2);
            $msg .= chr(0) . $l1 . $id . $l2 . $payload;
        }

        fwrite($sock, $msg);
        fclose($sock);

        $this->result['ios'] = $this->pushList['ios'];

    }

    /**
     * Sends the notifications
     */
    public function push() {

        $deviceTypes = array_keys($this->config);
        array_walk($deviceTypes, function($type) {

            $pushMethod = 'push' . $type;
            if (!method_exists($this, $pushMethod)) {
                $this->appendToErrors('There is no push method for device type "'. $type .'"');
                return;
            }

            $this->$pushMethod();

        });

    }

}