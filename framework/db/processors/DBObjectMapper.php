<?php

namespace Asymptix\db\processors;

use Asymptix\db\DBObject;

/**
 * Map DBObjects array and returns associated array with value of some field
 * of the DBObject.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class DBObjectMapper implements DBObjectProcessor
{
    /**
     * Field name.
     *
     * @var string
     */
    private $field = '';

    public function __construct($field)
    {
        $this->field = $field;
    }

    private function __map(DBObject $dbObject)
    {
        return $dbObject->getFieldValue($this->field);
    }

    public function __invoke(DBObject $dbObject)
    {
        return $this->__map($dbObject);
    }
}
