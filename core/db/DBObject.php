<?php

require_once(realpath(dirname(__FILE__)) . "/DBPreparedQuery.php");
require_once(realpath(dirname(__FILE__)) . "/DBSelector.php");

require_once(realpath(dirname(__FILE__)) . "/../Tools.php");
require_once(realpath(dirname(__FILE__)) . "/../Object.php");

require_once(realpath(dirname(__FILE__)) . "/../OutputStream.php");

/**
 * DBObject class. Object oriented representation of DB record.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
abstract class DBObject extends Object {

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
     * Create new default object.
     */
    public function DBObject() {}

    public function getId() {
        return $this->getFieldValue(static::ID_FIELD_NAME);
    }

    public function setId($id) {
        return $this->setFieldValue(static::ID_FIELD_NAME, $id);
    }

    public static function getIdFieldName() {
        return static::ID_FIELD_NAME;
    }

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
     * @throws Exception If record hos no 'activation' field.
     */
    public function isActivated() {
        $activation = $this->getFieldValue('activation');
        if (is_null($activation)) {
            throw new Exception("This object has no parameter 'activation'");
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
     * @throws Exception If record hos no 'removed' field.
     */
    public function isRemoved() {
        $isRemoved = $this->getFieldValue('removed');
        if (is_null($isRemoved)) {
            throw new Exception("This object has no parameter 'removed'");
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

    public function isNewRecord() {
        return ($this->id == 0);
    }

    public function save() {
        if ($this->getId() == 0) {
            $insertionId = DBCore::insertDBObject($this);
            if (Tools::isInteger($insertionId) && $insertionId > 0) {
                $this->setId($insertionId);
            } else {
                throw new Exception("Save database object error");
            }

            return $insertionId;
        } else {
            DBCore::updateDBObject($this);

            return $this->getId();
        }
    }

    /**
     * Prepare DBObject for the select query (for WHERE expression).
     *
     * @param array $conditions List of the conditions fields
     *           (fieldName => fieldValue or strCondition => params).
     *
     * @return DBObject Current object.
     */
    public function select($conditions = array()) {
        $this->dbQuery = new DBPreparedQuery();
        $this->dbQuery->conditions = $conditions;

        return $this;
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
        $this->dbQuery->query = "SELECT * FROM " . static::TABLE_NAME;

        /**
         * Conditions
         */
        if (!empty($this->dbQuery->conditions)) {
            $conditionsList = array();
            foreach ($this->dbQuery->conditions as $fieldName => $fieldValue) {
                if (!Tools::isInteger($fieldName)) {
                    $conditionsList[]= $fieldName . " = ?";
                    $this->dbQuery->types.= DBCore::getFieldType($fieldValue);
                    $this->dbQuery->params[] = $fieldValue;
                } else {
                    $condition = $fieldName;
                    $localParams = $fieldValue;

                    $conditionsList[] = "(" . $condition . ")";
                    foreach ($localParams as $param) {
                        $this->dbQuery->types.= DBCore::getFieldType($param);
                        $this->dbQuery->params[] = $param;
                    }
                }
            }

            $this->dbQuery->query.= " WHERE " . implode(" AND ", $conditionsList);
        }

        /**
         * Order
         */
        if (!empty($this->dbQuery->order)) {
            $this->dbQuery->query.= " ORDER BY";
            foreach ($this->dbQuery->order as $fieldName => $ord) {
                $this->dbQuery->query.= " " . $fieldName . " " . $ord . ",";
            }
        }
        $this->dbQuery->query = substr($this->dbQuery->query, 0, strlen($this->dbQuery->query) - 1);

        /**
         * Limit
         */
        if (!is_null($this->dbQuery->limit)) {
            if (Tools::isInteger($this->dbQuery->limit)) {
                $this->dbQuery->query.= " LIMIT " . $this->dbQuery->limit;
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

        if ($debug) {
            OutputStream::start();

            OutputStream::message(OutputStream::MSG_INFO, "QUERY: " . $this->dbQuery->query);
            OutputStream::message(OutputStream::MSG_INFO, "TYPES: " . $this->dbQuery->types);
            OutputStream::message(OutputStream::MSG_INFO, "PARAMS: [" . implode(", ", $this->dbQuery->params)  . "]");

            OutputStream::close();
        }

        $stmt = DBCore::doSelectQuery($this->dbQuery);
        if ($stmt !== false) {
            if ($stmt->num_rows > 1) {
                return DBCore::selectDBObjectsFromStatement($stmt, $this);
            } elseif ($stmt->num_rows == 1) {
                return DBCore::selectDBObjectFromStatement($stmt, $this);
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

?>