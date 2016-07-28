<?php

namespace Asymptix\core;

/**
 * Basic Object class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */

abstract class Object {

    /**
     * List of the database entity fields.
     *
     * @var array
     */
    protected $fieldsList;

    /**
     * List of fields aliases.
     *
     * @var array
     */
    protected $fieldsAliases = [];

    /**
     * Sets values of the object fields.
     *
     * @param array<string> $valuesList List of the values.
     *
     * @return mixed Number of added values or FALSE.
     */
    public function setFieldsValues($valuesList) {
        if (is_array($valuesList)) {
            $count = 0;
            foreach ($this->fieldsList as $fieldName => &$fieldValue) {
                if (isset($valuesList[$fieldName])) {
                    $newValue = $valuesList[$fieldName];
                    if (empty($newValue)) {
                        $fieldValue = self::getEmptyValue($fieldValue, $newValue);
                    } else {
                        $fieldValue = $newValue;
                    }
                    $count ++;
                } elseif (!empty($this->fieldsAliases)) { // look up for the field aliases
                    $fieldAliases = array_keys($this->fieldsAliases, $fieldName);
                    if (!empty($fieldAliases)) {
                        foreach ($fieldAliases as $alias) {
                            if (isset($valuesList[$alias])) {
                                $newValue = $valuesList[$alias];
                                if (empty($newValue)) {
                                    $fieldValue = self::getEmptyValue($fieldValue, $newValue);
                                } else {
                                    $fieldValue = $newValue;
                                }
                                $count ++;

                                break;
                            }
                        }
                    }
                }
            }
            return $count;
        } else {
            return false;
        }
    }

    /**
     * Sets value to the object's field.
     *
     * @param string $fieldName Name of the field.
     * @param mixed $fieldValue Value of the field.
     *
     * @return Object Object itself on success (for the method chaining support).
     * @throws \Exception If object has no field with such name.
     */
    public function setFieldValue($fieldName, $fieldValue) {
        if (isset($this->fieldsAliases[$fieldName])) {
            $fieldName = $this->fieldsAliases[$fieldName];
        }

        if (isset($this->fieldsList[$fieldName])) {
            $this->fieldsList[$fieldName] = $fieldValue;

            return $this;
        } else {
            throw new \Exception("Object '" . get_class($this) . "' hasn't field '" . $fieldName . "'");
        }
    }

    /**
     * Returns fields list array.
     *
     * @param bool $withAliases If this flag is `true` then we will have fields
     *           aliases in the result array as well.
     *
     * @return array
     */
    public function getFieldsList($withAliases = false) {
        if ($withAliases && !empty($this->fieldsAliases)) {
            $fieldsList = $this->fieldsList;
            foreach (array_keys($this->fieldsAliases) as $alias) {
                $fieldsList[$alias] = $this->getFieldValue($alias);
            }

            return $fieldsList;
        }

        return $this->fieldsList;
    }

    /**
     * Returns value of the field by it's name or alias.
     *
     * @param string $fieldName Field name or alias.
     *
     * @return mixed
     * @throws \Exception If object doesn't have this field or alias.
     */
    public function getFieldValue($fieldName) {
        if (isset($this->fieldsAliases[$fieldName])) {
            $fieldName = $this->fieldsAliases[$fieldName];
        }

        if (isset($this->fieldsList[$fieldName])) {
            return $this->fieldsList[$fieldName];
        } else {
            throw new \Exception("Object '" . get_class($this) . "' hasn't field '" . $fieldName . "'");
        }
    }

    /**
     * Returns type custed new empty field value.
     *
     * @param mixed $fieldValue Current field value.
     * @param mixed $newValue New value.
     *
     * @return mixed
     */
    private static function getEmptyValue($fieldValue, $newValue) {
        if (is_int($fieldValue)) {
            return 0;
        } elseif (is_string($fieldValue)) {
            return "";
        } elseif (is_null($fieldValue)) {
            return null;
        }

        return $newValue;
    }

    /**
     * Shows current object in structure view in the browser.
     */
    public function show() {
        print("<pre>");
        print_r($this);
        print("</pre>");
    }

    /**
     * Returns object's field name by getter/setter method name.
     *
     * @param string $methodNameFragment Method name fragment without 'get' or
     *            'set' prefix.
     * @return string Corresponded field name.
     */
    protected function getFieldName($methodNameFragment) {
        return lcfirst($methodNameFragment);
    }

    /**
     * Magic method to wrap getters and setters with own methods.
     *
     * @param string $methodName Name of the method.
     * @param array $methodParams Array of method parameters.
     *
     * @return mixed
     * @throws \Exception If some method is invalid or not exists.
     */
    public function __call($methodName, $methodParams) {
        $method = substr($methodName, 0, 3);
        $fieldName = $this->getFieldName(substr($methodName, 3));

        switch ($method) {
            case ("set"):
                $fieldValue = $methodParams[0];

                return $this->setFieldValue($fieldName, $fieldValue);
            case ("get"):
                return $this->getFieldValue($fieldName);
            default:
                throw new \Exception("No such method in the Object class.");
        }
    }

    /**
     * Magic method to wrap setters as fields values assignment.
     *
     * @param string $fieldName Name of the field.
     * @param mixed $fieldValue Value of the field.
     *
     * @return mixed The return value of the callback, or FALSE on error.
     */
    public function __set($fieldName, $fieldValue) {
        return call_user_func_array([$this, "set" . ucfirst($fieldName)], [$fieldValue]);
    }

    /**
     * Magic method to wrap getters as fields values calls.
     *
     * @param string $fieldName Name of the field.
     *
     * @return mixed The return value of the callback, or FALSE on error.
     */
    public function __get($fieldName) {
        return call_user_func_array([$this, "get" . ucfirst($fieldName)], []);
    }

}
