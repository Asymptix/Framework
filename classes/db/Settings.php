<?php

namespace db;

/**
 * Simple Settings bean class.
 * (You can modify this class according to your database structure)
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 * @license http://opensource.org/licenses/MIT
 */
class Settings extends \Asymptix\db\DBObject {

    const TABLE_NAME = "settings";
    const ID_FIELD_NAME = "setting_id";
    protected $fieldsList = array(
        'setting_id' => "", // varchar(100), not null, default ''
        'value' => "", // text, not null, default ''
    );

    public function save() {
        $query = "INSERT INTO " . self::TABLE_NAME . " (setting_id, value) VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE value = ?";
        return DBCore::doUpdateQuery($query, "sss", array(
            $this->id,
            $this->value,
            $this->value
        ));
    }

}