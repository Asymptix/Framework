<?php

namespace Asymptix\db;

/**
 * Database selecting functionality.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class DBSelector extends \Asymptix\core\Object
{
    protected $fieldsList = [
        'conditions' => '',
        'order'      => '',
        'offset'     => 0,
        'count'      => 'all',
        'field'      => '',
    ];

    /**
     * Inform DBSelector to select only unique records with DISTINCT.
     *
     * @var bool
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

    public function __construct($className)
    {
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
    private function validateClassName($className)
    {
        if ($className != $this->className) {
            $classNamespaceParts = explode('\\', $this->className);
            if ($className != $classNamespaceParts[count($classNamespaceParts) - 1]) {
                throw new DBSelectorException(
                    "Invalid DB object classname '".$className."' in method name. ".
                    "Valid classname is '".$this->className."'"
                );
            }
        }
    }

    /**
     * Reset fields to the initial values.
     */
    public function reset()
    {
        $this->setFieldsValues(
            [
                'conditions' => '',
                'order'      => '',
                'offset'     => 0,
                'count'      => 'all',
                'field'      => '',
            ]
        );
    }

    /**
     * Wrapper setter method for set SQL query conditions.
     *
     * @param mixed $conditions
     */
    public function setConditions($conditions)
    {
        if (is_array($conditions)) {
            $conditions = DBQueryCondition::getSQLCondition($conditions);
        }
        $this->setFieldValue('conditions', trim($conditions));
    }

    /**
     * Selects DBObject from the database.
     *
     * @param bool $debug Debug flag.
     *
     * @return DBObject
     */
    public function selectDBObject($debug = false)
    {
        $query = 'SELECT * FROM '.$this->dbObject->getTableName().
                  ($this->conditions != '' ? ' WHERE '.$this->conditions : '').' LIMIT 1';

        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query);
            if ($stmt !== false) {
                $dbObject = DBCore::selectDBObjectFromStatement($stmt, $this->dbObject);

                $stmt->close();

                return $dbObject;
            }

            return;
        }
        DBQuery::showQueryDebugInfo($query);
    }

    /**
     * Selects DBObject by some field value.
     *
     * @param string $fieldName  Name of the field.
     * @param mixed  $fieldValue Field value.
     * @param bool   $debug      Debug mode flag.
     *
     * @return DBObject
     */
    public function selectDBObjectByField($fieldName, $fieldValue, $debug = false)
    {
        $query = 'SELECT * FROM '.$this->dbObject->getTableName().' WHERE '.$fieldName.' = ?';

        if ($this->conditions != '') {
            $query .= ' AND '.$this->conditions;
        }

        $query .= $this->getQueryOrderSQL();
        $query .= ' LIMIT 1';

        $fieldType = DBField::getType($fieldValue);
        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query, $fieldType, [$fieldValue]);
            if ($stmt != false) {
                $dbObject = DBCore::selectDBObjectFromStatement($stmt, $this->dbObject);

                $stmt->close();

                return $dbObject;
            }

            return;
        }
        DBQuery::showQueryDebugInfo($query, $fieldType, [$fieldValue]);
    }

    /**
     * Selects DBObject by ID.
     *
     * @param mixed $objectId Id of the DB record (primary index).
     * @param bool  $debug    Debug mode flag.
     *
     * @return DBObject
     */
    public function selectDBObjectById($objectId = null, $debug = false)
    {
        if (is_null($objectId)) {
            $objectId = $this->dbObject->id;
        }

        return $this->selectDBObjectByField($this->dbObject->getIdFieldName(), $objectId, $debug);
    }

    /**
     * Selects DBObjects by some predefined condition.
     *
     * @param bool $debug Debug mode flag.
     *
     * @return array<DBObject>
     */
    public function selectDBObjects($debug = false)
    {
        $query = 'SELECT'.($this->unique ? ' DISTINCT' : '').' * FROM '.$this->dbObject->getTableName();

        if ($this->conditions != '') {
            $query .= ' WHERE '.$this->conditions;
        }

        $query .= $this->getQueryOrderSQL();
        $query .= $this->getQueryLimitSQL();

        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query);
            if ($stmt !== false) {
                $dbObjects = DBCore::selectDBObjectsFromStatement($stmt, $this->dbObject);

                $stmt->close();

                return $dbObjects;
            }

            return [];
        }
        DBQuery::showQueryDebugInfo($query);

        return [];
    }

    /**
     * Selects DBObjects by some field value.
     *
     * @param string $fieldName  Name of the field.
     * @param mixed  $fieldValue Field value.
     * @param bool   $debug      Debug mode flag.
     *
     * @return array<DBObject>
     */
    public function selectDBObjectsByField($fieldName, $fieldValue, $debug = false)
    {
        $query = 'SELECT * FROM '.$this->dbObject->getTableName();
        $query .= ' WHERE '.$fieldName.' = ?';

        if ($this->conditions != '') {
            $query .= ' AND '.$this->conditions;
        }

        $query .= $this->getQueryOrderSQL();
        $query .= $this->getQueryLimitSQL();

        $fieldType = DBField::getType($fieldValue);
        if (!$debug) {
            $stmt = DBCore::doSelectQuery($query, $fieldType, [$fieldValue]);
            if ($stmt != false) {
                $dbObjects = DBCore::selectDBObjectsFromStatement($stmt, get_class($this->dbObject));

                $stmt->close();

                return $dbObjects;
            }

            return [];
        }
        DBQuery::showQueryDebugInfo($query, $fieldType, [$fieldValue]);

        return [];
    }

    /**
     * Count number of records by some predefined condition.
     *
     * @return int Number of records.
     */
    public function count()
    {
        $query = 'SELECT count(*) FROM '.$this->dbObject->getTableName();

        if ($this->conditions != '') {
            $query .= ' WHERE '.$this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * Selects max value of some field by some predefined condition.
     *
     * @return int Number of records.
     */
    public function max()
    {
        $query = 'SELECT max(`'.$this->field.'`) FROM '.$this->dbObject->getTableName();

        if ($this->conditions != '') {
            $query .= ' WHERE '.$this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * Selects min value of some field by some predefined condition.
     *
     * @return int Number of records.
     */
    public function min()
    {
        $query = 'SELECT min(`'.$this->field.'`) FROM '.$this->dbObject->getTableName();

        if ($this->conditions != '') {
            $query .= ' WHERE '.$this->conditions;
        }

        return DBCore::selectSingleValue($query);
    }

    /**
     * Returns SQL ORDER string for current selector.
     *
     * @return string
     */
    private function getQueryOrderSQL()
    {
        if ($this->order != '') {
            return ' ORDER BY '.$this->order;
        }

        return ' ORDER BY '.$this->dbObject->getIdFieldName().' DESC';
    }

    /**
     * Returns SQL LIMIT string for current selector.
     *
     * @return string
     */
    private function getQueryLimitSQL()
    {
        if ($this->count !== 'all') {
            if ($this->offset > 0) {
                return ' LIMIT '.$this->offset.','.$this->count;
            }

            return ' LIMIT '.$this->count;
        }

        return '';
    }

    /**
     * Magic methods for readable method names.
     *
     * @param string $methodName   Name of the method.
     * @param array  $methodParams Method parameters.
     *
     * @throws DBSelectorException
     *
     * @return mixed
     */
    public function __call($methodName, $methodParams)
    {
        /*
         * Selects DBObject record by some field value.
         *
         * @param <mixed> Value of the field
         *
         * @return DBObject
         */
        if (preg_match('#^select([[:alpha:]]+)By([[:alpha:]]+)#', $methodName, $matches)) {
            if (empty($methodParams[0])) {
                return;
            }
            $this->validateClassName($matches[1]);

            $fieldName = substr(strtolower(preg_replace('#([A-Z]{1})#', '_$1', $matches[2])), 1);
            $fieldValue = $methodParams[0];

            if ($fieldName == 'id') {
                return $this->selectDBObjectById($fieldValue);
            }

            return $this->selectDBObjectByField($fieldName, $fieldValue);
        }
        /*
         * Selects all class of DBObject records from database by some order.
         *
         * @param string SQL order string (optional).
         *
         * @return array<DBObject>
         */
        elseif (preg_match('#^selectAll([A-Z]{1}[[:alpha:]]+)s#', $methodName, $matches)) {
            $this->validateClassName(preg_replace('#ie$#', 'y', $matches[1]));

            $this->order = '`'.$this->dbObject->getIdFieldName().'` DESC';
            if (isset($methodParams[0])) {
                $this->order = (string) $methodParams[0];
            }

            $dbObjects = $this->selectDBObjects();
            $this->reset();

            return $dbObjects;
        }
        /*
         * Selects DBObject records from database.
         *
         * @return array<DBObject>
         */
        elseif (preg_match('#^select([[:alpha:]]+)s#', $methodName, $matches)) {
            $this->validateClassName(preg_replace('#ie$#', 'y', $matches[1]));

            return $this->selectDBObjects();
        }
        /*
         * Selects DBObject record from database.
         *
         * @return array<DBObject>
         */
        elseif (preg_match('#^select([[:alpha:]]+)#', $methodName, $matches)) {
            $this->validateClassName($matches[1]);

            return $this->selectDBObject();
        }

       /*
        * Try to call parent method __call() with same params by default
        */
        $method = substr($methodName, 0, 3);
        $fieldName = $this->getFieldName(substr($methodName, 3));

        switch ($method) {
            case 'set':
                $fieldValue = $methodParams[0];

                return $this->setFieldValue($fieldName, $fieldValue);
            case 'get':
                return $this->getFieldValue($fieldName);
            default:
                throw new DBSelectorException("No method with name '".$methodName."'");
        }
    }
}

/**
 * Service exception class.
 */
class DBSelectorException extends \Exception
{
}
