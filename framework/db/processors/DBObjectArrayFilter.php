<?php

namespace Asymptix\db\processors;

use Asymptix\db\DBObject;

/**
 * Filter DBObjects array by presence of some field value in array.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBObjectArrayFilter implements DBObjectProcessor
{

    /**
     * Field name.
     *
     * @var string
     */
    private $field = "";

    /**
     * Array with field values.
     *
     * @var array
     */
    private $values = [];

    public function __construct($field, $values) {
        $this->field = $field;
        $this->values = $values;
    }

    private function __filter(DBObject $dbObject) {
        return in_array($dbObject->getFieldValue($this->field), $this->values);
    }

    public function __invoke(DBObject $dbObject) {
        return $this->__filter($dbObject);
    }

}
