<?php

namespace Asymptix\db\processors;

use Asymptix\db\DBObject;

/**
 * Map DBObjects array and returns associated array with values of some fields
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
class DBObjectArrayMapper implements DBObjectProcessor
{
    /**
     * Array with fields names.
     *
     * @var array
     */
    private $fields = [];

    public function __construct($fields = [])
    {
        $this->fields = $fields;
    }

    private function __map(DBObject $dbObject)
    {
        if (empty($this->fields)) {
            return $dbObject->getFieldsList();
        }

        $data = [];
        foreach ($this->fields as $field) {
            $data[$field] = $dbObject->getFieldValue($field);
        }

        return $data;
    }

    public function __invoke(DBObject $dbObject)
    {
        return $this->__map($dbObject);
    }
}
