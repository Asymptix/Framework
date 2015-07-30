<?php

require_once(realpath(dirname(__FILE__)) . "/../Tools.php");
require_once(realpath(dirname(__FILE__)) . "/../Object.php");

require_once(realpath(dirname(__FILE__)) . "/DBCore.php");
require_once(realpath(dirname(__FILE__)) . "/DBField.php");
require_once(realpath(dirname(__FILE__)) . "/DBQueryCondition.php");

/**
 * Database selecting functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
class DBSelector extends Object {
    protected $fieldsList = array(
        'conditions' => "",
        'order' => "",
        'offset' => 0,
        'count' => "all",
        'field' => ""
    );

    /**
     * TODO: add docs
     *
     * @var type
     */
    public $unique = false;

    /**
     * Class name of the selector objects.
     *
     * @var string
     */
    private $className = null;

    /**
     * Selector object.
     *
     * @var DBObject
     */
    private $dbObject = null;

    public function __construct($className) {
        if (is_object($className)) {
            $className = get_class($className);
        }
        $this->className = $className;
        $this->dbObject = new $className();
    }

    /**
     * Validate if classname of the selector equal to the select method
     * classname.
     *
     * @param string $className
     */
    private function validateClassName($className) {
        if ($className != $this->className) {
            throw new DBSelectorException("Invalid DB object classname in method name");
        }
    }

    /**
     * Reset fields to the initial values.
     */
    public function reset() {
        $this->setFieldsValues(
            array(
                'conditions' => "",
                'order' => "",
                'offset' => 0,
                'count' => "all",
                'field' => ""
            )
        );
    }

    /**
     * Wrapper setter method for set SQL query conditions.
     *
     * @param mixed $conditions
     */
    public function setConditions($conditions) {
        if (is_array($conditions)) {
            $conditions = DBQueryCondition::getSQLCondition($conditions);
        }
        $this->setFieldValue('conditions', trim($conditions));
    }

    /**
     * Selects DBObject from the database.
     *
     * @param boolean $debug Debug flag.
     * @return DBObject
     */
    public function selectDBObject($debug = false) {
        $query = "SELECT * FROM " . $this->dbObject->getTableName() .
                  ($this->conditions != ""?" WHERE " . $this->conditions:"") . " LIMIT 1";

        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query);
            if ($stmt !== false) {
                $dbObject = DBCore::selectDBObjectFromStatement($stmt, $this->dbObject);

                $stmt->close();
                return $dbObject;
            }
        } else {
            //TODO: add debug code
        }

        return null;
    }

    /**
     * TODO: add docs
     *
     * @param type $fieldName
     * @param type $fieldValue
     * @param type $debug
     *
     * @return type
     */
    public function selectDBObjectByField($fieldName, $fieldValue, $debug = false) {
        $query = "SELECT * FROM " . $this->dbObject->getTableName() . " WHERE " . $fieldName . " = ?";

        if ($this->conditions != "") {
            $query .= " AND " . $this->conditions;
        }

        if ($this->order != "") {
            $query .= " ORDER BY " . $this->order;
        } else {
            $query .= " ORDER BY " . $this->dbObject->getIdFieldName() . " DESC";
        }

        $query.= " LIMIT 1";

        $fieldType = DBField::getType($fieldValue);
        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query, $fieldType, array($fieldValue));
            if ($stmt != false) {
                $dbObject = DBCore::selectDBObjectFromStatement($stmt, $this->dbObject);
                $stmt->close();

                return $dbObject;
            }
        } else {
            //TODO: add debug code
        }

        return null;
    }

    /**
     * TODO: add docs
     *
     * @param type $objectId
     * @param type $debug
     * @return type
     */
    public function selectDBObjectById($objectId = null, $debug = false) {
        if (is_null($objectId)) {
            $objectId = $this->dbObject->id;
        }
        return $this->selectDBObjectByField($this->dbObject->getIdFieldName(), $objectId, $debug);
    }

    /**
     * TODO: add docs
     *
     * @param type $debug
     * @return type
     */
    public function selectDBObjects($debug = false) {
        $query = "SELECT" . ($this->unique?" DISTINCT":"") . " * FROM " . $this->dbObject->getTableName();

        if ($this->conditions != "") {
            $query .= " WHERE " . $this->conditions;
        }

        if ($this->order != "") {
            $query .= " ORDER BY " . $this->order;
        } else {
            $query .= " ORDER BY " . $this->dbObject->getIdFieldName() . " DESC";
        }

        if ($this->count !== "all") {
            if ($this->offset > 0) {
                $query .= " LIMIT " . $this->offset . "," . $this->count;
            } else {
                $query .= " LIMIT " . $this->count;
            }
        }

        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query);
            if ($stmt !== false) {
                $dbObjects = DBCore::selectDBObjectsFromStatement($stmt, $this->dbObject);
                $stmt->close();

                return $dbObjects;
            }
        } else {
            //TODO: add debug code
        }

        return array();
    }

    /**
     * TODO: add docs
     *
     * @param type $fieldName
     * @param type $fieldValue
     * @param type $debug
     *
     * @return type
     */
    public function selectDBObjectsByField($fieldName, $fieldValue, $debug = false) {
        $query = "SELECT * FROM " . $this->dbObject->getTableName();
        $query .= " WHERE " . $fieldName . " = ?";

        if ($this->conditions != "") {
            $query .= " AND " . $this->conditions;
        }

        if ($this->order != "") {
            $query .= " ORDER BY " . $this->order;
        } else {
            $query .= " ORDER BY " . $this->dbObject->getIdFieldName() . " DESC";
        }

        if ($this->count !== "all") {
            if ($this->offset > 0) {
                $query .= " LIMIT " . $this->offset . "," . $this->count;
            } else {
                $query .= " LIMIT " . $this->count;
            }
        }

        if (!$debug) {
            $fieldType = DBField::getType($fieldValue);
            $stmt = DBCore::doSelectQuery($query, $fieldType, array($fieldValue));
            if ($stmt != false) {
                $dbObjects = DBCore::selectDBObjectsFromStatement($stmt, get_class($this->dbObject));
                $stmt->close();

                return $dbObjects;
            }
        } else {
            //TODO: debug code
        }

        return array();
    }

    /**
     * TODO: add docs
     *
     * @return type
     */
    public function count() {
        $query = "SELECT count(*) FROM " . $this->dbObject->getTableName();

        if ($this->conditions != "") {
            $query .= " WHERE " . $this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * TODO: add docs
     *
     * @return type
     */
    public function max() {
        $query = "SELECT max(`" . $this->field . "`) FROM " . $this->dbObject->getTableName();

        if ($this->conditions != "") {
            $query .= " WHERE " . $this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * TODO: add docs
     *
     * @return type
     */
    public function min() {
        $query = "SELECT min(`" . $this->field . "`) FROM " . $this->dbObject->getTableName();

        if ($this->conditions != "") {
            $query .= " WHERE " . $this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * Magic methods for readable method names.
     *
     * @param string $methodName Name of the method.
     * @param array $methodParams Method parameters.
     *
     * @return mixed
     * @throws DBSelectorException
     */
    public function __call($methodName, $methodParams) {
        /**
         * Selects DBObject record by some field value.
         *
         * @param <mixed> Value of the field
         *
         * @return DBObject
         */
        if (preg_match("#^select([[:alpha:]]+)By([[:alpha:]]+)#", $methodName, $matches)) {
            if (empty($methodParams[0])) {
                return null;
            }
            $this->validateClassName($matches[1]);

            $fieldName = substr(strtolower(preg_replace("#([A-Z]{1})#", "_$1", $matches[2])), 1);
            $fieldValue = $methodParams[0];

            if ($fieldName == "id") {
                return $this->selectDBObjectById($fieldValue);
            }

            return $this->selectDBObjectByField($fieldName, $fieldValue);
        }
        /**
         * Selects all class of DBObject records from database by some order.
         *
         * @param string SQL order string (optional).
         *
         * @return array<DBObject>
         */
        elseif (preg_match("#^selectAll([A-Z]{1}[[:alpha:]]+)s#", $methodName, $matches)) {
            $this->validateClassName(preg_replace("#ie$#", "y", $matches[1]));

            $this->order = "ORDER BY `" . $dbObject->getIdFieldName() . "` DESC";
            if (isset($methodParams[0])) {
                $this->order = "ORDER BY " . (string)$methodParams[0];
            }

            $dbObjects = $this->selectDBObjects();
            $this->reset();

            return $dbObjects;
        }
        /**
         * Selects DBObject records from database.
         *
         * @return array<DBObject>
         */
        elseif (preg_match("#^select([[:alpha:]]+)s#", $methodName, $matches)) {
            $this->validateClassName(preg_replace("#ie$#", "y", $matches[1]));

            return $this->selectDBObjects();
        }
        /**
         * Selects DBObject record from database.
         *
         * @return array<DBObject>
         */
        elseif (preg_match("#^select([[:alpha:]]+)#", $methodName, $matches)) {
            $this->validateClassName($matches[1]);

            return $this->selectDBObject();
        }

       /*
        * Try to call parent method __call() with same params by default
        */
        $method = substr($methodName, 0, 3);
        $fieldName = $this->getFieldName(substr($methodName, 3));

        switch ($method) {
            case ("set"):
                $fieldValue = $methodParams[0];
                return $this->setFieldValue($fieldName, $fieldValue);
            case ("get"):
                return $this->getFieldValue($fieldName);
            default:
                throw new DBSelectorException("No method with name '" . $methodName . "'");
        }
    }
}

/**
 * Service exception class.
 */
class DBSelectorException extends Exception {};

?>