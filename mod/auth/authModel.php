<?php

/**
 * Class authModel
 *
 * The Model of user autnetication
 */

class authModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function authUser($uid, $secret) {

        $this->addField('uid');
        $this->addField('name');
        $this->addField('email');
        $this->addFrom('users');
        $this->addWhere('secret = "' . $secret . '"');
        $this->addWhere('uid = "' . $uid . '"');

        $this->runQuery();
        return !$this->isEmpty();
    }

    /**
     * Validates login by username and password
     *
     * @param   string      $user       - The Username
     * @param   string      $pass       - Must be MD5 hashed
     * @return  bool
     */
    public function checkLogin($user, $pass) {

        /**
         * This query is a suggestion.
         * Please implement your own authentication query
         * that is suitable to your database
         */

        $this->addField('u.*');

        $this->addFrom('users u');

        $this->addWhere('u.username = "' . $user . '"');
        $this->addWhere('u.passwd = "' . CR::encodeText($pass) . '"');
        $this->runQuery();

        return !$this->isEmpty();
    }


}