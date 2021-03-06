<?php

namespace Asymptix\db;

/**
 * Wraper for DBObject with fields for storing creation and updating time and
 * user ID.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2013 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBTimedObject extends DBObject {

    /**
     * Changes record creation time and user Id.
     *
     * @global DBObject $_USER User object.
     */
    private function changeCreateTime() {
        global $_USER;

        $this->createTime = date("Y-m-d H:i:s");
        $this->createUserId = $_USER->id;
    }

    /**
     * Changes record updating time and user Id.
     *
     * @global DBObject $_USER User object.
     */
    private function changeUpdateTime() {
        global $_USER;

        $this->updateTime = date("Y-m-d H:i:s");
        $this->updateUserId = $_USER->id;
    }

    /**
     * Save record to the database.
     *
     * @param bool $debug Debug mode flag.
     *
     * @return int Id of the record.
     */
    public function save($debug = false) {
        if ($this->isNewRecord()) { // new record
            $this->changeCreateTime();
        } else {
            $this->changeUpdateTime();
        }

        return parent::save($debug);
    }

    /**
     * Saves activation flag to the database.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveActivationFlag() {
        $this->changeUpdateTime();

        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET activation = ?,
                    update_time = ?,
                    update_user_id = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "isii",
            [$this->activation, $this->updateTime, $this->updateUserId, $this->id]
        );
    }

    /**
     * Saves removement flag to the database.
     *
     * @return int Returns the number of affected rows on success, and -1 if
     *            the last query failed.
     */
    public function saveRemovementFlag() {
        $this->changeUpdateTime();

        return DBCore::doUpdateQuery(
            "UPDATE " . static::TABLE_NAME . "
                SET removed = ?,
                    update_time = ?,
                    update_user_id = ?
             WHERE " . static::ID_FIELD_NAME . " = ?
             LIMIT 1",
            "isii",
            [$this->removed, $this->updateTime, $this->updateUserId, $this->id]
        );
    }

}
