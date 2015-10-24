<?php

/**
 * Class Services
 *
 * Dependency Injection class
 *
 * The intention is to create a
 * dependency injection that does not require
 * the service registration.
 *
 * All you will need is to call the module or library name
 *
 * Still in development
 */
class Services {

    /**
     * @var array
     */
    private static $services = array();


    /**
     * Returns the Service
     *
     * @param   string      $service        - Module or Library name
     * @return  mixed
     * @throws  ExceptionHandler
     */
    public static function get($service) {

        if (isset(self::$services[$service])) return self::$services[$service];

        if (class_exists($service)) {
            self::$services[$service] = new $service();
            return self::$services[$service];
        }

        $class = $service . 'Control';

        if (class_exists($class)) {
            self::$services[$service] = new $class();
            return self::$services[$service];
        }

        throw new ExceptionHandler('Service ' . $service . ' not found');

    }

}