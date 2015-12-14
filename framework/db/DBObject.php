<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * DBObject class. Object oriented representation of DB record.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
abstract class DBObject extends \Asymptix\core\Object {

    /**
     * Status constants
     */
    const STATUS_ACTIVATED = 1;
    const STATUS_DEACTIVATED = 0;

    const STATUS_REMOVED = 1;
    const STATUS_RESTORED = 0;

    /**
     * DB Query object for Prepared Statement
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
        return $this->getFieldValue(static::ID_FIELD_NAME);
    }

    /**
     * Sets primary key value.
     *
     * @param mixed $id Key vaue
     *
     * @return boolean Success flag.
     * @throws DBCoreException If object has no field with such name.
     */
    public function setId($id) {
        return $this->setFieldValue(static::ID_FIELD_NAME, $id);
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
     * @return integer Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveActivationFlag() {
        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET activation = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "ii",
            array($this->activation, $this->id)
        );
    }

    /**
     * Detects if current record is activated.
     *
     * @return boolean
     *
     * @throws DBCoreException If record hos no 'activation' field.
     */
    public function isActivated() {
        $activation = $this->getFieldValue('activation');
        if (is_null($activation)) {
            throw new DBCoreException("This object has no parameter 'activation'");
        } else {
            if ($activation > 0) {
                return true;
            }
            return false;
        }
    }

    /**
     * Activates record and save changes into the database.
     *
     * @return integer
     */
    public function activate() {
        $this->setFieldValue('activation', self::STATUS_ACTIVATED);

        return $this->saveActivationFlag();
    }

    /**
     * Deactivates record and save changes into the database.
     *
     * @return integer
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
     * @return integer Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveRemovementFlag() {
        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET removed = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "ii",
            array($this->removed, $this->id)
        );
    }

    /**
     * Detects if current record is removed.
     *
     * @return boolean
     *
     * @throws DBCoreException If record hos no 'removed' field.
     */
    public function isRemoved() {
        $isRemoved = $this->getFieldValue('removed');
        if (is_null($isRemoved)) {
            throw new DBCoreException("This object has no parameter 'removed'");
        } else {
            if ($isRemoved == self::STATUS_REMOVED) {
                return true;
            }
            return false;
        }
    }

    /**
     * Enable removed flag of the record and save changes into the database.
     *
     * @return integer
     */
    public function remove() {
        $this->setFieldValue('removed', self::STATUS_REMOVED);

        return $this->saveRemovementFlag();
    }

    /**
     * Disable removed flag of the record and save changes into the database.
     *
     * @return integer
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
     * @return boolean
     */
    public function isNewRecord() {
        return ($this->id == 0);
    }

    /**
     * Saves DBObject to the database. If this is a new object - INSERT SQL
     *           instruction executes, if existed one - UPDATE.
     *
     * @return mixed Primary key value.
     * @throws DBCoreException If some database error occurred.
     */
    public function save() {
        if ($this->isNewRecord()) {
            $insertionId = DBCore::insertDBObject($this);
            if (Tools::isInteger($insertionId) && $insertionId > 0) {
                $this->setId($insertionId);
            } else {
                throw new DBCoreException("Save database object error");
            }

            return $insertionId;
        } else {
            DBCore::updateDBObject($this);

            return $this->id;
        }
    }

    /**
     * Inits SQL query.
     *
     * @param string $queryType Type of the SQL query from types list from DBQuery.
     * @param array $conditions List of conditions for WHERE instruction.
     * @param array $fields List of fields for INSERT or UPDATE types of SQL queries.
     *
     * @return DBObject Itself.
     */
    public function initQuery($queryType, $conditions = array(), $fields = array()) {
        $this->dbQuery = new DBPreparedQuery();

        $this->dbQuery->setType($queryType);
        $this->dbQuery->conditions = $conditions;
        $this->dbQuery->fields = $fields;

        /**
         * Inits LIMIT if called dynamic select() or update() method.
         */
        if (is_null($this->dbQuery->limit)) {
            $backTrace = debug_backtrace(false, 2);
            if (!is_array($backTrace) && isset($backTrace[1])) {
                $prevCall = $backTrace[1];
                if (is_array($prevCall) && isset($prevCall['type'])) {
                    if ($prevCall['type'] == '->') { // called dynamic method
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
    public function select($conditions = array()) {
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
    public static function _select($conditions = array()) {
        $ref = new \ReflectionClass(get_called_class());
        $dbObject = $ref->newInstance();

        return $dbObject->initQuery(DBQueryType::SELECT, $conditions);
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
    public function update($fields = array(), $conditions = array(), $debug = false) {
        //TODO: decide if is needed below functionality
        /*if (!$this->isNewRecord()) { // Process only current record on fire.
            $this->initQuery(DBQueryType::UPDATE, $conditions, $fields);

            return $this->go($debug);
        }*/

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
    public static function _update($fields = array(), $conditions = array()) {
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
     * @param integer $offset Limit offset value (or count if this is single
     *           parameter).
     * @param integer $count Number of records to select.
     *
     * @return DBObject Current object.
     */
    public function limit($offset = 1, $count = null) {
        if (is_null($count)) {
            $this->dbQuery->limit = $offset;
        } else {
            $this->dbQuery->limit = array($offset, $count);
        }

        return $this;
    }

    /**
     * Selects DB record(s) for current DBObject table according to params.
     *
     * @param boolean $debug Debug mode flag.
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
        }

        /**
         * Conditions
         */
        if ($this->isNewRecord()) {
            if (!empty($this->dbQuery->conditions)) {
                $this->dbQuery->query.= " WHERE ";
                $this->dbQuery->sqlPushValues($this->dbQuery->conditions, " AND ");
            }
        } else {
            $this->dbQuery->query.= " WHERE ";
            $this->dbQuery->sqlPushValues(array(static::ID_FIELD_NAME => $this->id));
        }

        /**
         * Order
         */
        if ($this->isNewRecord()) {
            if (!empty($this->dbQuery->order)) {
                $this->dbQuery->query.= " ORDER BY";
                if (is_array($this->dbQuery->order)) {
                    foreach ($this->dbQuery->order as $fieldName => $ord) {
                        $this->dbQuery->query.= " " . $fieldName . " " . $ord . ",";
                    }
                    $this->dbQuery->query = substr($this->dbQuery->query, 0, strlen($this->dbQuery->query) - 1);
                } elseif (is_string($this->dbQuery->order)) {
                    $this->dbQuery->query.= " " . $this->dbQuery->order;
                }
            }
        }

        /**
         * Limit
         */
        $count = null;
        if ($this->isNewRecord()) {
            if (!is_null($this->dbQuery->limit)) {
                if (Tools::isInteger($this->dbQuery->limit)) {
                    $this->dbQuery->query.= " LIMIT " . $this->dbQuery->limit;
                    $count = $this->dbQuery->limit;
                } elseif (is_array($this->dbQuery->limit) && count($this->dbQuery->limit) == 2) {
                    $offset = $this->dbQuery->limit[0];
                    $count = $this->dbQuery->limit[1];
                    if (Tools::isInteger($offset) && Tools::isInteger($count)) {
                        $this->dbQuery->query.= " LIMIT " . $offset . ", " . $count;
                    } else {
                        throw new DBCoreException("Invalid LIMIT param in select() method.");
                    }
                } else {
                    throw new DBCoreException("Invalid LIMIT param in select() method.");
                }
            }
        } else {
            $this->dbQuery->query.= " LIMIT 1";
            $count = 1;
        }

        if ($debug) {
            DBQuery::showQueryDebugInfo(
                $this->dbQuery->query,
                $this->dbQuery->types,
                $this->dbQuery->params
            );
        } else {
            if ($this->dbQuery->isSelect()) {
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
            } else {
                return $this->dbQuery->go();
            }
        }

        return null;
    }

    /**
     * Deletes DB record of some entity by it's ID.
     *
     * @param mixed $id In of the DB record.
     *
     * @return mixed Number of affected rows (1 if some record was deleted,
     *            0 - if no) or FALSE if some error occurred.
     */
    public static function deleteObject($id) {
        if (!empty($id)) {
            $class = get_called_class();

            $obj = new $class;
            $obj->setId($id);

            return $obj->delete();
        }
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

    protected function getFieldName($methodNameFragment) {
        return substr(strtolower(preg_replace("#([A-Z]{1})#", "_$1", $methodNameFragment)), 1);
    }
}