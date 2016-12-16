<?php

namespace Asymptix\db\processors;

use Asymptix\db\DBObject;

/**
 * Filter DBObjects array by some field value.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBObjectFilter implements DBObjectProcessor
{

    /**
     * Field name.
     *
     * @var string
     */
    private $field = "";

    /**
     * Field value.
     *
     * @var mixed
     */
    private $value = null;

    public function __construct($field, $value) {
        $this->field = $field;
        $this->value = $value;
    }

    private function __filter(DBObject $dbObject) {
        return ($dbObject->getFieldValue($this->field) == $this->value);
    }

    public function __invoke(DBObject $dbObject) {
        return $this->__filter($dbObject);
    }

}
