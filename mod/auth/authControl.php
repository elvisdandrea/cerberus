<?php

/**
 * Class authControl
 *
 * The controller of the users
 * authentication
 *
 * The OAuth2 methods are those used
 * by the RESTful server. The non-RESTful
 * usage of this may use normal encrypted
 * authentication
 *
 */
class authControl extends Control {

    public function __construct() {
        parent::__construct();
    }


    /**
     * Authenticate via RESTful method
     *
     * This authentication method requires
     * the specification of the UID and Secret
     * instead of normal user and pass.
     *
     * This will also generate the access token
     * and refresh token to be used on RESTful
     * requests
     *
     */
    public function authenticate() {

        $this->model('auth')->authUser(
            $this->getQueryString('uid'),
            $this->getQueryString('secret')
        );

    }

    /**
     * Form user login
     *
     * This authentication method requires
     * user and password and will not generate
     * access token or refresh token
     */
    public function login() {

    }

}