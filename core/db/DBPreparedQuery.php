<?php

require_once(realpath(dirname(__FILE__)) . "/../Tools.php");

/**
 * Complex DB query object for Prepared Statement.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
class DBPreparedQuery {

    /**
     * DB query template.
     *
     * @var string
     */
    public $query = "";

    /**
     * Parameters SQL types string ("idsb").
     *
     * @var string
     */
    public $types = "";

    /**
     * List of the DB SQL query parameters.
     *
     * @var array
     */
    public $params = array();


    /* Service variables */

    /**
     * SQL conditions list.
     *
     * @var array
     */
    public $conditions = array();

    /**
     * SQL order list.
     *
     * @var array
     */
    public $order = null;

    /**
     * SQL limit value (may be pair array or integer value).
     *
     * @var mixed
     */
    public $limit = 1;

    /**
     * Creates and initialize DBPreparedQuery object.
     *
     * @param string $query DB SQL query template.
     * @param string $types Parameters SQL types string ("idsb").
     * @param array $params List of the DB SQL query parameters.
     */
    public function __construct($query = "", $types = "", $params = array()) {
        $this->query = $query;
        $this->types = $types;
        $this->params = $params;
    }

    /**
     * Verify if current DBPreparedQuery is have parameters for binding.
     *
     * @return boolean
     */
    public function isBindable() {
        return ($this->params != null && count($this->params) > 0);
    }

    /**
     * Verify if current DBPreparedQuery is valid for the execution.
     *
     * @return boolean
     */
    public function isValid() {
        //TODO: add verification of types string for only "idsb"
        return (strlen($this->types) == count($this->params));
    }

}

?>