<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * DB field representation class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2011 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBField {
    /**
     * Name of the field.
     *
     * @var string
     */
    public $name;

    /**
     * Value of the field.
     *
     * @var mixed
     */
    public $value;

    /**
     * Type of the field.
     *
     * @var string
     */
    public $type;

    /**
     * Constructor of the class with parameters validation.
     *
     * @param string $type Type of the field.
     * @param string $name Name of the field.
     * @param mixed $value Value of the field.
     */
    public function __construct($type = "", $name = "", $value = null) {
        if (!(bool)preg_match("#^[a-zA-Z][a-zA-Z0-9_]*$#", $name)) {
            throw new DBFieldException("Can't create DBField object: invalid field name '" . $name . "'");
        }
        $this->name = $name;

        if (empty($type)) {
            if (empty($value) && !is_null($value)) {
                throw new DBFieldException("Can't create DBField object: type and value are empty");
            } elseif (is_null($value)) {
                $this->type = null;
                $this->value = null;
            } else {
                $this->type = self::getType($value);
                $this->value = $value;
            }
        } else {
            $this->type = self::castType($type);
            if (!is_null($value)) {
                switch ($this->type) {
                    case ("i"):
                        $this->value = (int)$value;
                        break;
                    case ("d"):
                        $this->value = (float)$value;
                        break;
                    case ("s"):
                        $this->value = (string)$value;
                        break;
                    case ("b"):
                        $this->value = (bool)$value;
                        break;
                }
            }
        }
    }

    /**
     * Returns SQL type equivalent ("idsb") for common used types.
     *
     * @param string $fieldType Type of the field (example: "integer", "int",
     *           "double", "real", "bool", ...).
     *
     * @return string
     * @throws DBFieldTypeException If invalid field type passed.
     */
    public static function castType($fieldType) {
        $typesList = [
            'integer' => "i",
            'int'     => "i",
            'i'       => "i",
            'real'    => "d",
            'float'   => "d",
            'double'  => "d",
            'd'       => "d",
            'string'  => "s",
            'str'     => "s",
            's'       => "s",
            'boolean' => "b",
            'bool'    => "b",
            'b'       => "b"
        ];

        if (isset($typesList[$fieldType])) {
            return $typesList[$fieldType];
        }
        throw new DBFieldTypeException("Invalid SQL type");
    }

    /**
     * Returns type of the parameter by value.
     *
     * @param mixed $fieldValue
     *
     * @return string Types of the parameter ("idsb").
     * @throws DBFieldTypeException If can't detect field type by value.
     */
    public static function getType($fieldValue) {
        if (is_null($fieldValue)) { // Type is not principled for NULL
            return "i";
        } elseif (Tools::isInteger($fieldValue)) {
            return "i";
        } elseif (Tools::isDouble($fieldValue)) {
            return "d";
        } elseif (Tools::isBoolean($fieldValue)) {
            return "b";
        } elseif (Tools::isString($fieldValue)) {
            return "s";
        } else {
            throw new DBFieldTypeException(
                "Can't detect field value type for value '" . (string)$fieldValue . "'"
            );
        }
    }

    /**
     * Casts field value to it's type.
     *
     * @param string $type Field type.
     * @param mixed $value Field value.
     *
     * @return mixed Casted field value.
     * @throws DBFieldTypeException If invalid field type provided.
     */
    public static function castValue($type, $value) {
        switch (self::castType($type)) {
            case ("i"):
                return (int)$value;
            case ("d"):
                return (float)$value;
            case ("s"):
                return (string)$value;
            case ("b"):
                return (bool)$value;
        }

        throw new DBFieldTypeException("Invalid SQL type");
    }

    /**
     * Returns field value in the SQL query format.
     *
     * @param string $type Field type.
     * @param mixed $value Field value.
     *
     * @return mixed SQL formatted value of the field.
     * @throws DBFieldTypeException If invalid field type provided.
     */
    public static function sqlValue($type, $value) {
        if (is_null($value)) {
            return "NULL";
        }

        $type = self::castType($type);
        $value = self::castValue($type, $value);
        switch ($type) {
            case ("i"):
            case ("d"):
                return $value;
            case ("s"):
                if (!in_array($value, ['NOW()'])) {
                    return "'" . $value . "'";
                } else {
                    return $value;
                }
            case ("b"):
                if ($value) {
                    return "TRUE";
                }

                return "FALSE";
        }

        throw new DBFieldTypeException("Invalid SQL type");
    }

}

/**
 * Service exception classes.
 */
class DBFieldException extends \Exception {}
class DBFieldTypeException extends \Exception {}
