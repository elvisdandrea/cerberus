<?php

/**
 * Class homeModel
 *
 * This is where you place your queries
 * No logic should be coded here, query functions only
 */
class homeModel extends Model {

    /**
     * The constructor
     *
     * You may specify the connection name
     * ob the object instantiation
     *
     * @param   string      $connection     - The connection name used by this model
     */
    public function __construct($connection = DEFAULT_CONNECTION) {
        parent::__construct($connection);
    }

    /**
     * The Example of a Query
     */
    public function queryExample($id) {

        $this->addField('id');
        $this->addField('name');
        $this->addFrom('customer');
        $this->addWhere('id = "' . $id . '"');

        $this->runQuery();
        /**
         * At this point, the result will be on dataset property
         *
         * So, on the controller, you just call
         * $this->getResult() to get all rows in an array
         * or $this->getRow(0) (or any row you wish)
         */

        /**
         * A creative way of testing if there are results
         */
        return !$this->isEmpty();
    }

}