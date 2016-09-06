<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * Complex DB query object for Prepared Statement.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
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
    public $params = [];


    /* Service variables */

    /**
     * Creates and initialize DBPreparedQuery object.
     *
     * @param string $query DB SQL query template.
     * @param string $types Parameters SQL types string ("idsb").
     * @param array $params List of the DB SQL query parameters.
     */
    public function __construct($query = "", $types = "", $params = []) {
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
     * @return bool
     */
    public function isBindable() {
        return ($this->params != null && count($this->params) > 0);
    }

    /**
     * Verify if current DBPreparedQuery is valid for the execution.
     *
     * @return bool
     */
    public function isValid() {
        self::checkParameterTypes($this->params, $this->types);

        return true;
    }

    /**
     * Adds conditions WHERE SQL string to the SQL query.
     */
    public function prepareConditions() {
        if (!empty($this->conditions)) {
            $this->query.= " WHERE ";
            $this->sqlPushValues($this->conditions, " AND ");
        }
    }

    /**
     * Adds ORDER SQL string to the SQL query.
     */
    public function prepareOrder() {
        if (!empty($this->order)) {
            $this->query.= " ORDER BY";
            if (is_array($this->order)) {
                foreach ($this->order as $fieldName => $ord) {
                    $this->query.= " " . $fieldName . " " . $ord . ",";
                }
                $this->query = substr($this->query, 0, strlen($this->query) - 1);
            } elseif (is_string($this->order)) {
                $this->query.= " " . $this->order;
            }
        }
    }

    /**
     * Adds LIMIT SQL string to the SQL query.
     *
     * @return mixed Number of records will be selected or null.
     * @throws DBCoreException If some error occurred.
     */
    public function prepareLimit() {
        $count = null;
        if (!is_null($this->limit)) {
            if (Tools::isInteger($this->limit)) {
                $this->query.= " LIMIT " . $this->limit;
                $count = $this->limit;
            } elseif (is_array($this->limit) && count($this->limit) == 2) {
                $offset = $this->limit[0];
                $count = $this->limit[1];
                if (Tools::isInteger($offset) && Tools::isInteger($count)) {
                    $this->query.= " LIMIT " . $offset . ", " . $count;
                } else {
                    throw new DBCoreException("Invalid LIMIT param in select() method.");
                }
            } else {
                throw new DBCoreException("Invalid LIMIT param in select() method.");
            }
        }

        return $count;
    }

    /**
     * Prepares SQL query to the execution.
     *
     * @param string $query Initial SQL query string.
     * @param array $conditions Conditions list.
     * @param array $order List of order conditions (fieldName => order),
     *           order may be 'ASC' OR 'DESC'.
     * @param int $offset Limit offset value (or count if this is single
     *           parameter).
     * @param int $count Number of records to select.
     *
     * @return DBPreparedQuery Oneself after modifications.
     * @throws DBCoreException If some error occurred.
     */
    public function prepare($query, $conditions = null, $order = null, $offset = null, $count = null) {
        if (empty($query)) {
            throw new DBCoreException("Nothing to run, SQL query is not initialized");
        }
        $this->query = $query;

        if (!is_null($conditions)) {
            if (!is_array($conditions)) {
                throw new DBCoreException("Invalid conditions array");
            }
            $this->conditions = $conditions;
        }
        $this->prepareConditions();

        if (!is_null($order)) {
            $this->order = $order;
        }
        $this->prepareOrder();

        if (!is_null($offset)) {
            if (is_null($count)) {
                $this->dbQuery->limit = $offset;
            } else {
                $this->dbQuery->limit = [$offset, $count];
            }
        }
        $this->prepareLimit();

        return $this;
    }

    /**
     * Executes SQL query.
     *
     * @param bool $debug Debug mode flag.
     *
     * @return mixed Statement object or FALSE if an error occurred if SELECT
     *           query executed or number of affected rows on success if other
     *           type of query executed.
     */
    public function go($debug = false) {
        if ($debug) {
            $this->debug();
        } else {
            if ($this->isSelector()) {
                return DBCore::doSelectQuery($this);
            }

            return DBCore::doUpdateQuery($this);
        }
    }

    /**
     * Shows debug information for the SQL query without execution.
     */
    public function debug() {
        self::showQueryDebugInfo($this->query, $this->types, $this->params);
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
        if (count($params) != strlen($types)) {
            throw new DBCoreException(
                "Number of types is not equal parameters number"
            );
        }

        foreach ($params as $key => $value) {
            $type = $types[$key];

            if (!in_array($type, ['i', 'd', 's', 'b'])) {
                throw new DBCoreException(
                    "Invalid query parameters types string (type '" . $type .
                    "' is undefined, only 'i', 'd', 's' and 'b' types are acceptable)"
                );
            }

            $typeByValue = DBField::getType($value);
            if ($typeByValue != 's') {
                if ($type != $typeByValue && !(
                       ($type == 'd' && $typeByValue == 'i') || // We can put integer as double
                       ($type == 's' && $typeByValue == 'i') // We can put integer as string
                   )
                ) {
                    throw new DBCoreException(
                        "Invalid query parameters types string ('" . $value .
                        "' is not '" . $type . "' type but '" . $typeByValue . "' detected)"
                    );
                }
            } else { // in case if we try send non-string parameters as a string value
                switch ($type) {
                    case 'i':
                        if (!(Tools::isNumeric($value) && ((string)(int)$value === $value))) {
                            throw new DBCoreException(
                                "Invalid query parameters types string ('" . $value . "' is not '" . $type . ")"
                            );
                        }
                        break;
                    case 'd':
                        if (!Tools::isDoubleString($value)) {
                            throw new DBCoreException(
                                "Invalid query parameters types string ('" . $value . "' is not '" . $type . ")"
                            );
                        }
                        break;
                    case 'b':
                        if (!in_array(strtolower($value), ['true', 'false'])) {
                            throw new DBCoreException(
                                "Invalid query parameters types string ('" . $value . "' is not '" . $type . ")"
                            );
                        }
                        break;
                }
            }
        }
    }

    /**
     * Return qwestion marks string for IN(...) SQL construction.
     *
     * @param int $length Length of the result string.
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
        $chunks = [];
        foreach (array_keys($fieldsList) as $fieldName) {
            if ($fieldName != $idFieldName) {
                $chunks[] = "`" . $fieldName . "` = ?";
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
        $chunks = [];
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
     *    b - corresponding variable is a blob and will be sent in packets.
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
     * @param int $length Length of the SQL types string.
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
        $chunks = [];
        foreach ($values as $fieldName => $fieldValue) {
            if (!is_array($fieldValue)) {
                if (!is_null($fieldValue)) {
                    $chunks[] = $fieldName . " = ?";
                    $this->types.= DBField::getType($fieldValue);
                    $this->params[] = $fieldValue;
                } else {
                    $chunks[] = $fieldName;
                }
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
