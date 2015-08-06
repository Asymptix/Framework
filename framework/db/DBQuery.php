<?php

namespace Asymptix\DB;

use Asymptix\Core\OutputStream;

/**
 * DB SQL query object.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
class DBQuery {

    /* Service variables */

    /**
     * Type of the SQL query.
     *
     * @var string
     */
    protected $type = DBQueryType::SELECT;

    /**
     * SQL conditions list.
     *
     * @var array
     */
    public $conditions = array();

    /**
     * SQL fields list for INSERT/UPDATE queries.
     *
     * @var array
     */
    public $fields = array();

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
    public $limit = null;

    /**
     * Creates new DBQuery object with SQL type initialized.
     *
     * @param string $type SQL query type.
     */
    public function __construct($type = DBQueryType::SELECT) {
        $this->setType($type);
    }

    /**
     * Sets SQL query type with additional query type validation.
     *
     * @param string $type SQL query type.
     * @throws DBCoreException If invalid query type provided.
     */
    public function setType($type = DBQueryType::SELECT) {
        if (DBQueryType::isValidQueryType($type)) {
            $this->type = $type;
        } else {
            throw new DBCoreException("Invalid SQL query type '" . $type . "'");
        }
    }

    /**
     * Returns SQL query type.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Detects type of the SQL query.
     *
     * @return string Type of the SQL query.
     * @throws DBCoreException If SQL query is invalid.
     */
    protected function detectType() {
        return DBQueryType::detectQueryType($this->query);
    }

    /**
     * Outputs DB query debug information to the stream.
     *
     * @param string $query SQL query.
     * @param string $types SQL types string.
     * @param array $params List of SQL query parameters.
     */
    public static function showQueryDebugInfo($query = "", $types = "", array $params = array()) {
        if (!empty($query)) {
            OutputStream::start();

            OutputStream::message(OutputStream::MSG_INFO, "QUERY: " . $query);

            if (!empty($types)) {
                OutputStream::message(OutputStream::MSG_INFO, "TYPES: " . $types);
            }

            if (!empty($params)) {
                OutputStream::message(OutputStream::MSG_INFO, "PARAMS: [" . implode(", ", $params)  . "]");
            }

            OutputStream::close();
        }
    }

}

?>