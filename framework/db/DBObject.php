<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * DBObject class. Object oriented representation of DB record.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
abstract class DBObject extends \Asymptix\core\Object {

    /**
     * Status constants.
     */
    const STATUS_ACTIVATED = 1;
    const STATUS_DEACTIVATED = 0;

    const STATUS_REMOVED = 1;
    const STATUS_RESTORED = 0;

    /**
     * DB Query object for Prepared Statement.
     *
     * @var DBPreparedQuery
     */
    private $dbQuery = null;

    /**
     * Creates new default object.
     */
    public function __construct() {}

    /**
     * Returns primary key value.
     *
     * @return mixed.
     */
    public function getId() {
        if (is_null(static::ID_FIELD_NAME)) {
            return null;
        }

        return $this->getFieldValue(static::ID_FIELD_NAME);
    }

    /**
     * Sets primary key value.
     *
     * @param mixed $recordId Key vaue.
     *
     * @return bool Success flag.
     * @throws DBCoreException If object has no field with such name.
     */
    public function setId($recordId) {
        return $this->setFieldValue(static::ID_FIELD_NAME, $recordId);
    }

    /**
     * Returns name of the primary key field.
     *
     * @return mixed
     */
    public static function getIdFieldName() {
        return static::ID_FIELD_NAME;
    }

    /**
     * Returns DBObject table name.
     *
     * @return string
     */
    public static function getTableName() {
        return static::TABLE_NAME;
    }

    /**
     * Saves activation flag to the database.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveActivationFlag() {
        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET activation = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "ii",
            [$this->activation, $this->id]
        );
    }

    /**
     * Detects if current record is activated.
     *
     * @return bool
     *
     * @throws DBCoreException If record hos no 'activation' field.
     */
    public function isActivated() {
        $activation = $this->getFieldValue('activation');
        if (is_null($activation)) {
            throw new DBCoreException("This object has no parameter 'activation'");
        }

        return ($activation > 0);
    }

    /**
     * Activates record and save changes into the database.
     *
     * @return int
     */
    public function activate() {
        $this->setFieldValue('activation', self::STATUS_ACTIVATED);

        return $this->saveActivationFlag();
    }

    /**
     * Deactivates record and save changes into the database.
     *
     * @return int
     */
    public function deactivate() {
        $this->setFieldValue('activation', self::STATUS_DEACTIVATED);

        return $this->saveActivationFlag();
    }

    /**
     * Changes record activation flag and save changes into the database.
     */
    public function changeActivation() {
        if ($this->isActivated()) {
            $this->deactivate();
        } else {
            $this->activate();
        }
    }

    /**
     * Saves removement flag to the database.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveRemovementFlag() {
        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET removed = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "ii",
            [$this->removed, $this->id]
        );
    }

    /**
     * Detects if current record is removed.
     *
     * @return bool
     *
     * @throws DBCoreException If record hos no 'removed' field.
     */
    public function isRemoved() {
        $isRemoved = $this->getFieldValue('removed');
        if (is_null($isRemoved)) {
            throw new DBCoreException("This object has no parameter 'removed'");
        }

        return ($isRemoved == self::STATUS_REMOVED);
    }

    /**
     * Enable removed flag of the record and save changes into the database.
     *
     * @return int
     */
    public function remove() {
        $this->setFieldValue('removed', self::STATUS_REMOVED);

        return $this->saveRemovementFlag();
    }

    /**
     * Disable removed flag of the record and save changes into the database.
     *
     * @return int
     */
    public function restore() {
        $this->setFieldValue('removed', self::STATUS_RESTORED);

        return $this->saveRemovementFlag();
    }

    /**
     * Changes record removement flag and save changes into the database.
     */
    public function changeRemovement() {
        if ($this->isRemoved()) {
            $this->restore();
        } else {
            $this->remove();
        }
    }

    /**
     * Detects if current DBObject represents not existed DB record.
     *
     * @return bool
     */
    public function isNewRecord() {
        if (is_null(static::ID_FIELD_NAME)) {
            return true;
        }

        return ($this->id == 0);
    }

    /**
     * Saves DBObject to the database. If this is a new object - INSERT SQL
     *           instruction executes, if existed one - UPDATE.
     *
     * @param bool $debug Debug mode flag.
     *
     * @return mixed Primary key value.
     * @throws DBCoreException If some database error occurred.
     */
    public function save($debug = false) {
        if ($this->isNewRecord()) {
            $insertionId = DBCore::insertDBObject($this, false, $debug);
            if (Tools::isInteger($insertionId) && $insertionId > 0) {
                $this->setId($insertionId);

                return $insertionId;
            }
            throw new DBCoreException("Save database object error");
        }
        DBCore::updateDBObject($this, $debug);

        return $this->id;
    }

    /**
     * Inserts DBObject to the database.
     *
     * @param bool $ignore Ignore unique indexes or not.
     * @param bool Debug mode flag.
     *
     * @return mixed Primary key value.
     * @throws DBCoreException If some database error occurred.
     */
    public function insert($ignore = false, $debug = false) {
        return DBCore::insertDBObject($this, $ignore, $debug);
    }

    /**
     * Inits SQL query.
     *
     * @param string $queryType Type of the SQL query from types list from DBQuery.
     * @param array $conditions List of conditions for WHERE instruction.
     * @param array $fields List of fields for INSERT or UPDATE types of SQL queries.
     *
     * @return DBObject Oneself.
     * @throws DBCoreException If some error occurred.
     */
    public function initQuery($queryType, $conditions = [], $fields = []) {
        $this->dbQuery = new DBPreparedQuery();

        $this->dbQuery->setType($queryType);

        if (!is_array($conditions)) {
            throw new DBCoreException("Invalid conditions array");
        }
        $this->dbQuery->conditions = $conditions;

        if (!is_array($fields)) {
            throw new DBCoreException("Invalid fields array");
        }
        $this->dbQuery->fields = $fields;

        /*
         * Inits LIMIT if called dynamic select() or update() method.
         */
        if (is_null($this->dbQuery->limit)) {
            $backTrace = debug_backtrace();
            if (is_array($backTrace) && isset($backTrace[1])) {
                $prevCall = $backTrace[1];
                if (is_array($prevCall) && isset($prevCall['type'])) {
                    if ($prevCall['type'] == '->') { // dynamic method was called
                        $this->dbQuery->limit = 1;
                    }
                }
            }
            unset($backTrace);
        }

        return $this;
    }

    /**
     * Prepare DBObject for the SELECT SQL query.
     *
     * @param array $conditions List of the conditions fields
     *           (fieldName => fieldValue or sqlCondition => params).
     *
     * @return DBObject Current object.
     */
    public function select($conditions = []) {
        return $this->initQuery(DBQueryType::SELECT, $conditions);
    }

    /**
     * Static way to prepare DBObject for the SELECT SQL query.
     *
     * @param array $conditions List of the conditions fields
     *           (fieldName => fieldValue or sqlCondition => params).
     *
     * @return DBObject Current object.
     */
    public static function _select($conditions = []) {
        $ref = new \ReflectionClass(get_called_class());
        $dbObject = $ref->newInstance();

        return $dbObject->initQuery(DBQueryType::SELECT, $conditions);
    }

    /**
     * Select and returns DB record for current DBObject table by record ID.
     *
     * @param mixed $recordId Record ID.
     * @param bool $debug Debug mode flag.
     *
     * @return DBObject Record object or null.
     */
    public static function _get($recordId, $debug = false) {
        return static::_select([
            static::ID_FIELD_NAME => $recordId
        ])->limit(1)->go($debug);
    }

    /**
     * Returns result of the COUNT() SQL query.
     *
     * @param array $conditions Conditions list.
     * @param type $debug Debug mode flag.
     *
     * @return int
     */
    public static function _count($conditions = [], $debug = false) {
        $dbQuery = (new DBPreparedQuery())->prepare(
            "SELECT COUNT(*) as 'val' FROM " . static::TABLE_NAME,
            $conditions
        );

        if (!$debug) {
            return (int)DBCore::selectSingleValue($dbQuery);
        }
        $dbQuery->debug();
    }

    /**
     * Returns result of the MAX($field) SQL query.
     *
     * @param string $field Name of the field.
     * @param array $conditions Conditions list.
     * @param type $debug Debug mode flag.
     *
     * @return int
     */
    public static function _max($field, $conditions = [], $debug = false) {
        $dbQuery = (new DBPreparedQuery())->prepare(
            "SELECT MAX(`" . $field . "`) as 'val' FROM " . static::TABLE_NAME,
            $conditions
        );

        if (!$debug) {
            return DBCore::selectSingleValue($dbQuery);
        }
        $dbQuery->debug();
    }

    /**
     * Returns result of the MIN($field) SQL query.
     *
     * @param string $field Name of the field.
     * @param array $conditions Conditions list.
     * @param type $debug Debug mode flag.
     *
     * @return int
     */
    public static function _min($field, $conditions = [], $debug = false) {
        $dbQuery = (new DBPreparedQuery())->prepare(
            "SELECT MIN(`" . $field . "`) as 'val' FROM " . static::TABLE_NAME,
            $conditions
        );

        if (!$debug) {
            return DBCore::selectSingleValue($dbQuery);
        }
        $dbQuery->debug();
    }

    /**
     * Prepare DBObject for the UPDATE SQL query.
     *
     * @param type $fields List of fields to be updated
     *           (fieldName => fieldValue or sqlAssignment => params).
     * @param array $conditions List of the conditions fields
     *           (fieldName => fieldValue or sqlCondition => params).
     *
     * @return DBObject Current object.
     */
    public function update($fields = [], $conditions = []) {
        return $this->initQuery(DBQueryType::UPDATE, $conditions, $fields);
    }

    /**
     * Static way to prepare DBObject for the UPDATE SQL query.
     *
     * @param type $fields List of fields to be updated
     *           (fieldName => fieldValue or sqlAssignment => params).
     * @param array $conditions List of the conditions fields
     *           (fieldName => fieldValue or sqlCondition => params).
     *
     * @return DBObject Current object.
     */
    public static function _update($fields = [], $conditions = []) {
        $ref = new \ReflectionClass(get_called_class());
        $dbObject = $ref->newInstance();

        return $dbObject->initQuery(DBQueryType::UPDATE, $conditions, $fields);
    }

    /**
     * Prepare DBObject for the select query (for ORDER expression).
     *
     * @param array $order List of order conditions (fieldName => order),
     *           order may be 'ASC' OR 'DESC'.
     *
     * @param array $order
     * @return DBObject Current object.
     */
    public function order($order = null) {
        $this->dbQuery->order = $order;

        return $this;
    }

    /**
     * Prepare DBObject for the select query (for LIMIT expression).
     *
     * @param int $offset Limit offset value (or count if this is single
     *           parameter).
     * @param int $count Number of records to select.
     *
     * @return DBObject Current object.
     */
    public function limit($offset = 1, $count = null) {
        if (is_null($offset)) {
            return $this;
        }

        if (is_null($count)) {
            $this->dbQuery->limit = $offset;
        } else {
            $this->dbQuery->limit = [$offset, $count];
        }

        return $this;
    }

    /**
     * Selects DB record(s) for current DBObject table according to params.
     *
     * @param bool $debug Debug mode flag.
     *
     * @return mixed DBObject, array of DBObject or null.
     * @throws DBCoreException If some DB or query syntax errors occurred.
     */
    public function go($debug = false) {
        switch ($this->dbQuery->getType()) {
            case (DBQueryType::SELECT):
                $this->dbQuery->query = "SELECT * FROM " . static::TABLE_NAME;
                break;
            case (DBQueryType::UPDATE):
                $this->dbQuery->query = "UPDATE " . static::TABLE_NAME . " SET ";
                $this->dbQuery->sqlPushValues($this->dbQuery->fields);
                break;
            case (DBQueryType::DELETE):
                $this->dbQuery->query = "DELETE FROM " . static::TABLE_NAME;
                break;
        }

        /*
         * Conditions
         */
        if ($this->isNewRecord()) {
            $this->dbQuery->prepareConditions();
        } else {
            $this->dbQuery->query.= " WHERE ";
            $this->dbQuery->sqlPushValues([static::ID_FIELD_NAME => $this->id]);
        }

        /*
         * Order
         */
        if ($this->isNewRecord()) {
            $this->dbQuery->prepareOrder();
        }

        /*
         * Limit
         */
        $count = null;
        if ($this->isNewRecord()) {
            $count = $this->dbQuery->prepareLimit();
        } else {
            $this->dbQuery->query.= " LIMIT 1";
            $count = 1;
        }

        if ($debug) {
            $this->dbQuery->debug();
        } else {
            if ($this->dbQuery->isSelector()) {
                $stmt = $this->dbQuery->go();
                if ($stmt !== false) {
                    $data = null;
                    if ($count !== 1) {
                        $data = DBCore::selectDBObjectsFromStatement($stmt, $this);
                    } else {
                        $data = DBCore::selectDBObjectFromStatement($stmt, $this);
                    }
                    $stmt->close();

                    return $data;
                }

                return null;
            }

            return $this->dbQuery->go();
        }

        return null;
    }

    /**
     * Deletes DB record for current DBObject.
     *
     * @return mixed Number of affected rows (1 if some record was deleted,
     *            0 - if no) or FALSE if some error occurred.
     */
    public function delete() {
        return DBCore::deleteDBObject($this);
    }

    /**
     * Deletes DB record by ID or condition.
     *
     * @param mixed $conditions List of the conditions fields
     *           (fieldName => fieldValue or sqlCondition => params).
     *           or ID value of the record
     * @return DBObject Current object.
     */
    public static function _delete($conditions = []) {
        $ref = new \ReflectionClass(get_called_class());
        $dbObject = $ref->newInstance();

        if (!is_array($conditions)) { // Just record ID provided
            $recordId = $conditions;
            $conditions = [
                $dbObject->getIdFieldName() => $recordId
            ];
            $dbObject->initQuery(DBQueryType::DELETE, $conditions);
            $dbObject->dbQuery->limit = 1;

            return $dbObject;
        }

        return $dbObject->initQuery(DBQueryType::DELETE, $conditions);
    }

    /**
     * Returns DB table field name by it's camelcase variant.
     *
     * @param string $methodNameFragment
     *
     * @return string
     */
    protected function getFieldName($methodNameFragment) {
        return substr(strtolower(preg_replace("#([A-Z]{1})#", "_$1", $methodNameFragment)), 1);
    }

}
