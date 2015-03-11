<?php

require_once("core/Tools.php");
require_once("core/Object.php");

/**
 * DBObject class. Object oriented representation of DB record.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
abstract class DBObject extends Object {

    const STATUS_ACTIVATED = 1;
    const STATUS_DEACTIVATED = 0;

    const STATUS_REMOVED = 1;
    const STATUS_RESTORED = 0;

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