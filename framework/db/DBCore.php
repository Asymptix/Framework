<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * Core database functionality.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2017, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBCore {
    /**
     * An array containing all the opened connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The incremented index of connections.
     *
     * @var int
     */
    protected $index = 0;

    /**
     * Current connection index.
     *
     * @var int
     */
    protected $currIndex = 0;

    /**
     * Instance of a class.
     *
     * @var DBCore
     */
    protected static $instance;

    /**
     * Returns an instance of this class.
     *
     * @return DBCore
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Reset the internal static instance.
     */
    public static function resetInstance() {
        if (self::$instance) {
            self::$instance->reset();
            self::$instance = null;
        }
    }

    /**
     * Reset this instance of the manager.
     */
    public function reset() {
        foreach ($this->connections as $conn) {
            $conn->close();
        }
        $this->connections = [];
        $this->index = 0;
        $this->currIndex = 0;
    }

    /**
     * Seves a new connection to DBCore->connections.
     *
     * @param mysqli Object $connResource An object which represents the connection to a MySQL Server.
     * @param string $connName Name of the connection, if empty numeric key is used.
     *
     * @throws DBCoreException If trying to save a connection with an existing name.
     */
    public static function connection($connResource = null, $connName = null) {
        if ($connResource == null) {
            return self::getInstance()->getCurrentConnection();
        }
        self::getInstance()->openConnection($connResource, $connName);
    }

    /**
     * Seves a new connection to DBCore->connections.
     *
     * @param mysqli Object $connResource An object which represents the connection to a MySQL Server.
     * @param string $connName Name of the connection, if empty numeric key is used.
     *
     * @throws DBCoreException If trying to save a connection with an existing name.
     */
    public function openConnection($connResource, $connName = null) {
        if ($connName !== null) {
            $connName = (string)$connName;
            if (isset($this->connections[$connName])) {
                throw new DBCoreException("You trying to save a connection with an existing name");
            }
        } else {
            $connName = $this->index;
            $this->index++;
        }

        $this->connections[$connName] = $connResource;
    }

    /**
     * Get the connection instance for the passed name.
     *
     * @param string $connName Name of the connection, if empty numeric key is used.
     *
     * @return mysqli Object
     *
     * @throws DBCoreException If trying to get a non-existent connection.
     */
    public function getConnection($connName) {
        if (!isset($this->connections[$connName])) {
            throw new DBCoreException('Unknown connection: ' . $connName);
        }

        return $this->connections[$connName];
    }

    /**
     * Get the name of the passed connection instance.
     *
     * @param mysqli Object $connResource Connection object to be searched for.
     *
     * @return string The name of the connection.
     */
    public function getConnectionName($connResource) {
        return array_search($connResource, $this->connections, true);
    }

    /**
     * Closes the specified connection.
     *
     * @param mixed $connection Connection object or its name.
     */
    public function closeConnection($connection) {
        $key = false;
        if (Tools::isObject($connection)) {
            $connection->close();
            $key = $this->getConnectionName($connection);
        } elseif (is_string($connection)) {
            $key = $connection;
        }

        if ($key !== false) {
            unset($this->connections[$key]);

            if ($key === $this->currIndex) {
                $key = key($this->connections);
                $this->currIndex = ($key !== null) ? $key : 0;
            }
        }

        unset($connection);
    }

    /**
     * Returns all opened connections.
     *
     * @return array
     */
    public function getConnections() {
        return $this->connections;
    }

    /**
     * Sets the current connection to $key.
     *
     * @param mixed $key The connection key
     *
     * @throws DBCoreException
     */
    public function setCurrentConnection($key) {
        if (!$this->contains($key)) {
            throw new DBCoreException("Connection key '$key' does not exist.");
        }
        $this->currIndex = $key;
    }

    /**
     * Whether or not the DBCore contains specified connection.
     *
     * @param mixed $key The connection key
     *
     * @return bool
     */
    public function contains($key) {
        return isset($this->connections[$key]);
    }

    /**
     * Returns the number of opened connections.
     *
     * @return int
     */
    public function count() {
        return count($this->connections);
    }

    /**
     * Returns an ArrayIterator that iterates through all connections.
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->connections);
    }

    /**
     * Get the current connection instance.
     *
     * @throws DBCoreException If there are no open connections
     *
     * @return mysqli Object
     */
    public function getCurrentConnection() {
        $key = $this->currIndex;
        if (!isset($this->connections[$key])) {
            throw new DBCoreException('There is no open connection');
        }

        return $this->connections[$key];
    }

    /**
     * Check database errors.
     *
     * @param object $dbObj
     */
    private static function checkDbError($dbObj) {
        if ($dbObj->error != "") {
            throw new DBCoreException($dbObj->error);
        }
    }

    /**
     * Bind parameters to the statment with dynamic number of parameters.
     *
     * @param resource $stmt Statement.
     * @param string $types Types string.
     * @param array $params Parameters.
     */
    private static function bindParameters($stmt, $types, $params) {
        $args   = [];
        $args[] = $types;

        foreach ($params as &$param) {
            $args[] = &$param;
        }
        call_user_func_array([$stmt, 'bind_param'], $args);
    }

    /**
     * Return parameters from the statment with dynamic number of parameters.
     *
     * @param resource $stmt Statement.
     */
    public static function bindResults($stmt) {
        $resultSet = [];
        $metaData = $stmt->result_metadata();
        $fieldsCounter = 0;
        while ($field = $metaData->fetch_field()) {
            if (!isset($resultSet[$field->table])) {
                $resultSet[$field->table] = [];
            }
            $resultSet[$field->table][$field->name] = $fieldsCounter++;
            $parameterName = "variable" . $fieldsCounter; //$field->name;
            $$parameterName = null;
            $parameters[] = &$$parameterName;
        }
        call_user_func_array([$stmt, 'bind_result'], $parameters);
        if ($stmt->fetch()) {
            foreach ($resultSet as &$tableResult) {
                foreach ($tableResult as &$fieldValue) {
                    $fieldValue = $parameters[$fieldValue];
                }
            }

            return $resultSet;
        }
        self::checkDbError($stmt);

        return null;
    }

    /**
     * Execute DB SQL queries using Prepared Statements.
     *
     * @param mixed $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     *
     * @return mixed Statement object or FALSE if an error occurred.
     */
    private static function doQuery($query, $types = "", $params = []) {
        if (!Tools::isInstanceOf($query, new DBPreparedQuery())) {
            $dbQuery = new DBPreparedQuery($query, $types, $params);
        } else {
            $dbQuery = $query;
        }

        $stmt = self::connection()->prepare($dbQuery->query);
        self::checkDbError(self::connection());

        if ($dbQuery->isBindable()) {
            if ($dbQuery->isValid()) {
                self::bindParameters($stmt, $dbQuery->types, $dbQuery->params);
            } else {
                throw new DBCoreException(
                    "Number of types is not equal parameters number or types string is invalid"
                );
            }
        }

        $stmt->execute();
        self::checkDbError($stmt);

        return $stmt;
    }

    /**
     * Execute DB SQL queries using Prepared Statements.
     *
     * @param mixed $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     *
     * @return mixed Statement object or FALSE if an error occurred.
     */
    public static function query($query, $types = "", $params = []) {
        return (new DBPreparedQuery($query, $types, $params))->go();
    }

    /**
     * Execute update DB SQL queries using Prepared Statements.
     *
     * @param string $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string.
     * @param array $params Parameters.
     *
     * @return int Returns the number of affected rows on success and
     *           -1 if the last query failed.
     */
    public static function doUpdateQuery($query, $types = "", $params = []) {
        if (!Tools::isInstanceOf($query, new DBPreparedQuery())) {
            $dbQuery = new DBPreparedQuery($query, $types, $params);
        } else {
            $dbQuery = $query;
        }
        $stmt = self::doQuery($dbQuery);

        switch ($dbQuery->getType()) {
            case (DBQueryType::INSERT):
                $result = self::connection()->insert_id;
                break;
            case (DBQueryType::UPDATE):
                $result = self::connection()->affected_rows;
                break;
            default:
                $result = self::connection()->affected_rows;
        }
        $stmt->close();

        return $result;
    }

    /**
     * Execute select DB SQL queries using Prepared Statements.
     *
     * @param mixed $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     *
     * @return mixed Statement object or FALSE if an error occurred.
     */
    public static function doSelectQuery($query, $types = "", $params = []) {
        $stmt = self::doQuery($query, $types, $params);

        $stmt->store_result();
        self::checkDbError($stmt);

        return $stmt;
    }

    /**
     * Returns list of database table fields.
     *
     * @param string $tableName Name of the table.
     * @return array<string> List of the database table fields (syntax: array[fieldName] = fieldType)
     */
    public static function getTableFieldsList($tableName) {
        if (!empty($tableName)) {
            $query = "SHOW FULL COLUMNS FROM " . $tableName;
            $stmt = self::doSelectQuery($query);
            if ($stmt !== false) {
                $stmt->bind_result(
                    $field, $type, $collation, $null, $key, $default, $extra, $privileges, $comment
                );

                $fieldsList = [];
                while ($stmt->fetch()) {
                    $fieldsList[$field] = [
                        'type' => $type,
                        'collation' => $collation,
                        'null' => $null,
                        'key' => $key,
                        'default' => $default,
                        'extra' => $extra,
                        'privileges' => $privileges,
                        'comment' => $comment
                    ];
                }
                $stmt->close();

                return $fieldsList;
            }
        }

        return [];
    }

    /**
     * Returns printable SQL field value for table fields list generator.
     *
     * @param string $type SQL type of the field.
     * @param mixed $value Field value.
     *
     * @return string
     */
    private static function getPrintableSQLValue($type, $value) {
        if (strpos($type, "varchar") === 0
         || strpos($type, "text") === 0
         || strpos($type, "longtext") === 0
         || strpos($type, "enum") === 0
         || strpos($type, "char") === 0
         || strpos($type, "datetime") === 0
         || strpos($type, "timestamp") === 0
         || strpos($type, "date") === 0) {
            return ('"' . $value . '"');
        } elseif (strpos($type, "int") === 0
         || strpos($type, "tinyint") === 0
         || strpos($type, "smallint") === 0
         || strpos($type, "mediumint") === 0
         || strpos($type, "bigint") === 0) {
            if (!empty($value)) {
                return $value;
            }

            return "0";
        } elseif (strpos($type, "float") === 0
         || strpos($type, "double") === 0
         || strpos($type, "decimal") === 0) {
            if (!empty($value)) {
                return $value;
            }

            return "0.0";
        }

        return $value;
    }

    /**
     * Returns printable field description string for table fields list generator.
     *
     * @param string $field Field name.
     * @param array $attributes List of field attributes.
     *
     * @return string
     */
    public static function getPrintableFieldString($field, $attributes) {
        $extra = trim($attributes['extra']);
        $comment = trim($attributes['comment']);

        $fieldStr = "'" . $field . "' => ";
        if ($attributes['null'] === 'YES' && is_null($attributes['default'])) {
            $fieldStr.= "null";
        } else {
            $fieldStr.= self::getPrintableSQLValue($attributes['type'], $attributes['default']);
        }
        $fieldStr.= ", // " . $attributes['type'] .
            ", " . (($attributes['null'] == "NO") ? "not null" : "null")
            . ", default '" . $attributes['default'] . "'" .
            ($extra ? ", " . $extra : "") .
            ($comment ? " (" . $comment . ")" : "") . "\n";

        return $fieldStr;
    }

    /**
     * Outputs comfortable for Bean Class creation list of table fields.
     *
     * @param string $tableName Name of the Db table.
     */
    public static function displayTableFieldsList($tableName) {
        print("<pre>");
        if (!empty($tableName)) {
            $fieldsList = self::getTableFieldsList($tableName);
            if (!empty($fieldsList)) {
                foreach ($fieldsList as $field => $attributes) {
                    print(self::getPrintableFieldString($field, $attributes));
                }
            }
        }
        print("</pre>");
    }

    /**
     * Returns list of fields values with default indexes.
     *
     * @param array<mixed> $fieldsList List of the table fields (syntax: array[fieldName] = fieldValue)
     * @param string $idFieldName Name of the primary key field.
     * @return array<mixed>
     */
    public static function createValuesList($fieldsList, $idFieldName = "") {
        $valuesList = [];
        foreach ($fieldsList as $fieldName => $fieldValue) {
            if ($fieldName != $idFieldName) {
                $valuesList[] = $fieldValue;
            }
        }

        return $valuesList;
    }

    /**
     * Executes SQL INSERT query to the database.
     *
     * @param DBObject $dbObject DBObject to insert.
     * @param bool $ignore Ignore unique indexes or not.
     * @param bool $debug Debug mode flag.
     *
     * @return int Insertion ID (primary key value) or null on debug.
     */
    public static function insertDBObject($dbObject, $ignore = false, $debug = false) {
        $fieldsList = $dbObject->getFieldsList();
        $idFieldName = $dbObject->getIdFieldName();

        if (Tools::isInteger($fieldsList[$idFieldName])) {
            $query = "INSERT " . ($ignore ? 'IGNORE' : 'INTO') . " " . $dbObject->getTableName() . "
                          SET " . DBPreparedQuery::sqlQMValuesString($fieldsList, $idFieldName);
            $typesString = DBPreparedQuery::sqlTypesString($fieldsList, $idFieldName);
            $valuesList = self::createValuesList($fieldsList, $idFieldName);
        } else {
            $query = "INSERT " . ($ignore ? 'IGNORE' : 'INTO') . " " . $dbObject->getTableName() . "
                          SET " . DBPreparedQuery::sqlQMValuesString($fieldsList);
            $typesString = DBPreparedQuery::sqlTypesString($fieldsList);
            $valuesList = self::createValuesList($fieldsList);
        }

        if ($debug) {
            DBQuery::showQueryDebugInfo($query, $typesString, $valuesList);

            return null;
        }
        self::doUpdateQuery($query, $typesString, $valuesList);

        return (self::connection()->insert_id);
    }

    /**
     * Executes SQL UPDATE query to the database.
     *
     * @param DBObject $dbObject DBObject to update.
     * @param bool $debug Debug mode flag.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *           the last query failed.
     */
    public static function updateDBObject($dbObject, $debug = false) {
        $fieldsList = $dbObject->getFieldsList();
        $idFieldName = $dbObject->getIdFieldName();

        $query = "UPDATE " . $dbObject->getTableName() . "
                  SET " . DBPreparedQuery::sqlQMValuesString($fieldsList, $idFieldName) . "
                  WHERE " . $idFieldName . " = ?
                  LIMIT 1";
        $typesString = DBPreparedQuery::sqlTypesString($fieldsList, $idFieldName);
        if (Tools::isInteger($fieldsList[$idFieldName])) {
            $typesString.= "i";
        } else {
            $typesString.= "s";
        }
        $valuesList = self::createValuesList($fieldsList, $idFieldName);
        $valuesList[] = $dbObject->getId();

        if ($debug) {
            DBQuery::showQueryDebugInfo($query, $typesString, $valuesList);
        } else {
            return self::doUpdateQuery($query, $typesString, $valuesList);
        }
    }

    /**
     * Executes SQL DELETE query to the database.
     *
     * @param DBObject $dbObject DBObject to delete.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *           the last query failed.
     */
    public static function deleteDBObject($dbObject) {
        if (!empty($dbObject) && is_object($dbObject)) {
            $query = "DELETE FROM " . $dbObject->getTableName() .
                     " WHERE " . $dbObject->getIdFieldName() . " = ? LIMIT 1";

            $typesString = "s";
            if (Tools::isInteger($dbObject->getId())) {
                $typesString = "i";
            }

            return self::doUpdateQuery(
                $query, $typesString, [$dbObject->getId()]
            );
        }

        return false;
    }

    /**
     * Returns DBObject from ResultSet.
     *
     * @param DBObject $dbObject
     * @param array $resultSet Associated by table names arrays of selected
     *           fields.
     *
     * @return DBObject
     */
    public static function selectDBObjectFromResultSet($dbObject, $resultSet) {
        $dbObject->setFieldsValues($resultSet[$dbObject->getTableName()]);

        return $dbObject;
    }

    /**
     * Returns DB object by database query statement.
     *
     * @param resource $stmt Database query statement.
     * @param string $className Name of the DB object class.
     * @return DBObject
     */
    public static function selectDBObjectFromStatement($stmt, $className) {
        if (is_object($className)) {
            $className = get_class($className);
        }

        if ($stmt->num_rows == 1) {
            $resultSet = self::bindResults($stmt);
            $dbObject = new $className();
            self::selectDBObjectFromResultSet($dbObject, $resultSet);

            if (!is_null($dbObject) && is_object($dbObject) && $dbObject->getId()) {
                return $dbObject;
            } else {
                return null;
            }
        } elseif ($stmt->num_rows > 1) {
            throw new DBCoreException("More than single record of '" . $className . "' entity selected");
        }

        return null;
    }

    /**
     * Selects DBObject from database.
     *
     * @param string $query SQL query.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     * @param mixed $instance Instance of the some DBObject class or it's class name.
     *
     * @return DBObject Selected DBObject or NULL otherwise.
     */
    public static function selectDBObject($query, $types, $params, $instance) {
        $stmt = self::doSelectQuery($query, $types, $params);
        $obj = null;
        if ($stmt) {
            $obj = self::selectDBObjectFromStatement($stmt, $instance);

            $stmt->close();
        }

        return $obj;
    }

    /**
     * Returns list of DB objects by database query statement.
     *
     * @param resource $stmt Database query statement.
     * @param mixed $className Instance of the some DBObject class or it's class name.
     *
     * @return array<DBObject>
     */
    public static function selectDBObjectsFromStatement($stmt, $className) {
        if (is_object($className)) {
            $className = get_class($className);
        }

        if ($stmt->num_rows > 0) {
            $objectsList = [];
            while ($resultSet = self::bindResults($stmt)) {
                $dbObject = new $className();
                self::selectDBObjectFromResultSet($dbObject, $resultSet);

                $recordId = $dbObject->getId();
                if (!is_null($recordId)) {
                    $objectsList[$recordId] = $dbObject;
                } else {
                    $objectsList[] = $dbObject;
                }
            }

            return $objectsList;
        }

        return [];
    }

    /**
     * Selects DBObject list from database.
     *
     * @param string $query SQL query.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     * @param mixed $instance Instance of the some DBObject class or it's class name.
     *
     * @return DBObject Selected DBObject or NULL otherwise.
     */
    public static function selectDBObjects($query, $types, $params, $instance) {
        $stmt = self::doSelectQuery($query, $types, $params);
        $obj = null;
        if ($stmt) {
            $obj = self::selectDBObjectsFromStatement($stmt, $instance);

            $stmt->close();
        }

        return $obj;
    }

    /**
     * Executes SQL query with single record and return this record.
     *
     * @param mixed $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     *
     * @return array Selected record with table names as keys or NULL if no
     *           data selected.
     * @throws DBCoreException If no one or more than one records selected.
     */
    public static function selectSingleRecord($query, $types = "", $params = []) {
        if (!Tools::isInstanceOf($query, new DBPreparedQuery())) {
            $dbQuery = new DBPreparedQuery($query, $types, $params);
        } else {
            $dbQuery = $query;
        }
        $stmt = $dbQuery->go();

        if ($stmt !== false) {
            $record = null;
            if ($stmt->num_rows === 1) {
                $record = self::bindResults($stmt);
            }
            $stmt->close();

            if (is_null($record)) {
                throw new DBCoreException("No one or more than one records selected.");
            }

            return $record;
        }

        return null;
    }

    /**
     * Executes SQL query with single record and value result and return this value.
     *
     * @param mixed $query SQL query template string or DBPreparedQuery object
     *           if single parameter.
     * @param string $types Types string (ex: "isdb").
     * @param array $params Parameters in the same order like types string.
     *
     * @return mixed
     * @throws DBCoreException If no one or more than one records selected.
     */
    public static function selectSingleValue($query, $types = "", $params = []) {
        if (!Tools::isInstanceOf($query, new DBPreparedQuery())) {
            $dbQuery = new DBPreparedQuery($query, $types, $params);
        } else {
            $dbQuery = $query;
        }
        $stmt = $dbQuery->go();

        if ($stmt !== false) {
            $value = null;
            $numRows = $stmt->num_rows;
            if ($numRows === 1) {
                $stmt->bind_result($value);
                $stmt->fetch();
            }
            $stmt->close();

            if ($numRows !== 1) {
                throw new DBCoreException("No one or more than one records selected.");
            }

            return $value;
        }

        return null;
    }

}

/**
 * Service exception class.
 */
class DBCoreException extends \Exception {}
