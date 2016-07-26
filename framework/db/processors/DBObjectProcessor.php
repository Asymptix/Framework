<?php

namespace Asymptix\db\processors;

use Asymptix\db\DBObject;

/**
 * DBObject processor interface.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
interface DBObjectProcessor
{
    public function __invoke(DBObject $dbObject);
}
