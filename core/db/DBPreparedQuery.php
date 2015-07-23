<?php

require_once(realpath(dirname(__FILE__)) . "/DBQuery.php");

require_once(realpath(dirname(__FILE__)) . "/../Tools.php");
require_once(realpath(dirname(__FILE__)) . "/../OutputStream.php");

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
class DBPreparedQuery extends DBQuery {

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

        if (!empty($this->query)) {
            $this->type = $this->detectType();
        }
        parent::__construct($this->type);
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
        self::checkParameterTypes($this->params, $this->types);

        return true;
    }

    /**
     * Executes SQL query.
     *
     * @param boolean $debug Debug mode flag.
     *
     * @return mixed Statement object or FALSE if an error occurred if SELECT
     *           query executed or number of affected rows on success if other
     *           type of query executed.
     */
    public function go($debug = false) {
        if ($debug) {
            OutputStream::start();

            OutputStream::message(OutputStream::MSG_INFO, "QUERY: " . $this->dbQuery->query);
            OutputStream::message(OutputStream::MSG_INFO, "TYPES: " . $this->dbQuery->types);
            OutputStream::message(OutputStream::MSG_INFO, "PARAMS: [" . implode(", ", $this->dbQuery->params)  . "]");

            OutputStream::close();
        }

        if ($this->getType() == DBQuery::TYPE_SELECT) {
            return DBCore::doSelectQuery($this);
        } else {
            return DBCore::doUpdateQuery($this);
        }
    }

    /**
     * Checks query parameters types correspondence.
     *
     * @param array $params Parameters of the query.
     * @param string $types Types of the parameters ("idsb").
     *
     * @throws DBCoreException
     */
    private static function checkParameterTypes($params, $types) {
        if (count($params) == strlen($types)) {
            foreach ($params as $key => $value) {
                $type = $types[$key];

                if (!in_array($type, array('i', 'd', 's', 'b'))) {
                    throw new DBCoreException(
                        "Invalid query parameters types string (type '" . $type .
                        "' is undefined, only 'i', 'd', 's' and 'b' types are acceptable)"
                    );
                }

                $typeByValue = self::getFieldType($value);
                if ($typeByValue != 's') {
                    if ($type != $typeByValue && !($type == 'd' && $typeByValue == 'i')) {
                        throw new DBCoreException("Invalid query parameters types string ('" . $value . "' is not '" . $type . "' type but '" . $typeByValue . "' detected)");
                    }
                } else { // in case if we try send non-string parameters as a string value
                    switch ($type) {
                        case 'i':
                            if (!(Tools::isNumeric($value) && ((string)(integer)$value === $value))) {
                                throw new DBCoreException("Invalid query parameters types string ('" . $value . "' is not '" . $type . ")");
                            }
                            break;
                        case 'd':
                            if (!Tools::isDoubleString($value)) {
                                throw new DBCoreException("Invalid query parameters types string ('" . $value . "' is not '" . $type . ")");
                            }
                            break;
                        case 'b':
                            if (!in_array(strtolower($value), array('true', 'false'))) {
                                throw new DBCoreException("Invalid query parameters types string ('" . $value . "' is not '" . $type . ")");
                            }
                            break;
                    }
                }
            }
        } else {
            throw new DBCoreException("Number of types is not equal parameters number");
        }
    }

    /**
     * Returns type of the parameter by it's value.
     *
     * @param mixed $fieldValue
     *
     * @return string Types of the parameter ("idsb").
     *
     * @throws Exception
     */
    public static function getFieldType($fieldValue) {
        if (Tools::isInteger($fieldValue)) {
            return "i";
        } elseif (Tools::isDouble($fieldValue)) {
            return "d";
        } elseif (Tools::isBoolean($fieldValue)) {
            return "b";
        } elseif (Tools::isString($fieldValue)) {
            return "s";
        } else {
            throw new Exception("Invalid field value type");
        }
    }

}

?>