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
        self::checkParameterTypes($this->params, $this->types);

        return true;
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