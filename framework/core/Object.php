<?php

namespace Asymptix\core;

/**
 * Basic Object class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */

abstract class Object {
    /**
     * List of the database entity fields.
     *
     * @var array<mixed>
     */
    protected $fieldsList;

    /**
     * Create new default object.
     */
    public function Object() {}

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
                        if (is_integer($fieldValue)) {
                            $fieldValue = 0;
                        } elseif (is_string($fieldValue)) {
                            $fieldValue = "";
                        } elseif (is_null($fieldValue)) {
                            $fieldValue = null;
                        } else {
                            $fieldValue = $newValue;
                        }
                    } else {
                        $fieldValue = $newValue;
                    }
                    $count ++;
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
     * @return boolean TRUE on success.
     * @throws \Exception If object has no field with such name.
     */
    public function setFieldValue($fieldName, $fieldValue) {
        if (isset($this->fieldsList[$fieldName])) {
            $this->fieldsList[$fieldName] = $fieldValue;
            return true;
        } else {
            throw new \Exception("Object '" . get_class($this) . "' hasn't field '" . $fieldName . "'");
        }
    }

    /**
     * Returns fields list array.
     *
     * @return array
     */
    public function getFieldsList() {
        return $this->fieldsList;
    }

    /**
     * TODO: add docs
     *
     * @param type $fieldName
     * @return type
     * @throws \Exception
     */
    public function getFieldValue($fieldName) {
        if (isset($this->fieldsList[$fieldName])) {
            return $this->fieldsList[$fieldName];
        } else {
            throw new \Exception("Object '" . get_class($this) . "' hasn't field '" . $fieldName . "'");
        }
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
        return call_user_func_array(array($this, "set" . ucfirst($fieldName)), array($fieldValue));
        //return $this->setFieldValue($this->getFieldName($fieldName), $fieldValue);
    }

    /**
     * Magic method to wrap getters as fields values calls.
     *
     * @param string $fieldName Name of the field.
     *
     * @return mixed The return value of the callback, or FALSE on error.
     */
    public function __get($fieldName) {
        return call_user_func_array(array($this, "get" . ucfirst($fieldName)), array());
        //return $this->getFieldValue($this->getFieldName($fieldName));
    }
}