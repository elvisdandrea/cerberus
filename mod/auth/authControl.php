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

        $queryString = $this->getQueryString();

        if ($queryString['token'])
            return $this->tokenAuthentication($queryString['id'], $queryString['token']);

        $this->newModel('auth');
        $auth = $this->model('auth')->authUser(
            $queryString['uid'],
            $queryString['secret']
        );

        if ($auth) {

        }

        if (!$auth)
            return RestServer::throwError(Language::UNAUTHORIZED(), 401);


    }

    /**
     * Validates the authentication token
     *
     * @param   string      $id         - The user uid
     * @param   string      $token      - The access token
     * @return  array
     * @throws  ExceptionHandler
     */
    private function tokenAuthentication($id, $token) {

        $tokenFile = MAINDIR . '/../.token/' . $id . '/' . $token;

        if (!is_file($tokenFile))
            return RestServer::throwError(Language::UNAUTHORIZED(), 401);

        $content = file_get_contents($tokenFile);
        $content = json_decode($content, true);

        if (!$content)
            return RestServer::throwError(Language::UNAUTHORIZED(), 401);

        foreach (array('expires', 'remote_addr') as $validation)
            if (!in_array($validation, array_keys($content)))
                return RestServer::throwError(Language::UNAUTHORIZED(), 401);

        if ($content['remote_addr'] != Core::getRemoteAddress())
            return RestServer::throwError(Language::UNAUTHORIZED(), 401);

        $tokenExpires = new DateTime($content['expires']);
        $currentDate  = new DateTime('now');

        if ($currentDate > $tokenExpires)
            return RestServer::throwError(Language::EXPIRED(), 401);
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

    /**
     * POST Method to create new user
     */
    public function postAddUser() {

        $post = $this->getPost();

        if (!$this->validatePost('name', 'user', 'pass'))
            return RestServer::throwError(Language::CANNOT_BE_BLANK('Name, User and Pass'), 400);


        $userData = array(
            'name'      => $post['name'],
            'username'  => $post['user'],
            'passwd'    => CR::encrypt($post['pass']),
            'email'     => $post['email']
        );

        $this->newModel('auth');
        $this->model('auth')->insertUser($userData);

        if (!$this->model('auth')->queryOk()) {
            if ($this->model('auth')->getErrorCode() == 23000)
                return RestServer::throwError(Language::USER_ALREADY_TAKEN(), 400);
            else
                return RestServer::throwError(Language::QUERY_ERROR(), 500);
        }

        return RestServer::response(array(
            'status'    => 200,
            'uid'       => $this->model('auth')->getLastInsertId(),
            'message'   => 'User created!'
        ), 200);
    }

    public function createUser() {

        if ($this->getPost('pass') != $this->getPost('passrepeat'))
            $this->commitReplace('Password does not match!', '#alert', false);

        $user = $this->postAddUser();
        $this->commitReplace($user['message'], '#alert', false);
    }

}