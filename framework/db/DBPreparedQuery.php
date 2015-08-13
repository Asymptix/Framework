<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

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
            DBQuery::showQueryDebugInfo($this->query, $this->types, $this->params);
        } else {
            if ($this->getType() == DBQueryType::SELECT) {
                return DBCore::doSelectQuery($this);
            } else {
                return DBCore::doUpdateQuery($this);
            }
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

                $typeByValue = DBField::getType($value);
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
     * Return qwestion marks string for IN(...) SQL construction.
     *
     * @param integer $length Length of the result string.
     *
     * @return string
     */
    public static function sqlQMString($length) {
        return implode(",", array_fill(0, $length, "?"));
    }

    /**
     * Return fields and qwestion marks string for SET field1=?, ... SQL construction.
     *
     * @param array<mixed> $fieldsList List of the table fields (syntax: array[fieldName] = fieldValue)
     * @param string $idFieldName Name of the primary key field.
     *
     * @return string
     */
    public static function sqlQMValuesString($fieldsList, $idFieldName = "") {
        $chunks = array();
        foreach ($fieldsList as $fieldName => $fieldValue) {
            if ($fieldName != $idFieldName) {
                $chunks[]= "`" . $fieldName . "` = ?";
            }
        }
        return implode(", ", $chunks);
    }

    /**
     * Return fields and values string for SET field1=value1, ... SQL construction.
     *
     * @param array<mixed> $fieldsList List of the table fields (syntax: array[fieldName] = fieldValue)
     * @param string $idFieldName Name of the primary key field.
     *
     * @return string
     */
    public static function sqlValuesString($fieldsList, $idFieldName) {
        $chunks = array();
        foreach ($fieldsList as $fieldName => $fieldValue) {
            if ($fieldName != $idFieldName) {
                $chunks[]= "`" . $fieldName . "` = '" . $fieldValue . "'";
            }
        }
        return implode(", ", $chunks);
    }

    /**
     * Returns SQL types string.
     * Type specification chars:
     *    i - corresponding variable has type integer
     *    d - corresponding variable has type double
     *    s - corresponding variable has type string
     *    b - corresponding variable is a blob and will be sent in packets
     *
     * @param array<mixed> $fieldsList List of the table fields (syntax: array[fieldName] = fieldValue)
     * @param string $idFieldName Name of the primary key field.
     * @return string
     */
    public static function sqlTypesString($fieldsList, $idFieldName = "") {
        $typesString = "";
        foreach ($fieldsList as $fieldName => $fieldValue) {
            if ($fieldName != $idFieldName) {
                if (Tools::isDouble($fieldValue)) {
                    $typesString.= "d";
                } elseif (Tools::isInteger($fieldValue)) {
                    $typesString.= "i";
                } else {
                    $typesString.= "s";
                }
            }
        }
        return $typesString;
    }

    /**
     * Returns SQL types string of single type.
     *
     * @param string $type SQL type.
     * @param integer $length Length of the SQL types string.
     *
     * @return string
     * @throws DBFieldTypeException If invalid type passed.
     */
    public static function sqlSingleTypeString($type, $length) {
        $type = DBField::castType($type);
        $typesString = "";
        while ($length > 0) {
            $typesString.= $type;
            $length--;
        }

        return $typesString;
    }

    /**
     * Push values to the DBPreparedQuery SQL query field end.
     *
     * @param array $values List of pairs key => value or SQL query parts with
     *           parameters.
     * @param string $separator Join separator.
     */
    public function sqlPushValues($values, $separator = ", ") {
        $chunks = array();
        foreach ($values as $fieldName => $fieldValue) {
            if (!is_array($fieldValue)) {
                $chunks[]= $fieldName . " = ?";
                $this->types.= DBField::getType($fieldValue);
                $this->params[] = $fieldValue;
            } else {
                $condition = $fieldName;
                $localParams = $fieldValue;

                $chunks[] = $condition;
                foreach ($localParams as $param) {
                    $this->types.= DBField::getType($param);
                    $this->params[] = $param;
                }
            }
        }
        $this->query.= implode($separator, $chunks);
    }

}

?>