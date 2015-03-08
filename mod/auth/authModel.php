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


}