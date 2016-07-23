<?php

namespace db; // TODO: change on needed namespace

/**
 * TODO: change class description
 *
 * @author {{AUTHOR}} <{{EMAIL}}>
 * @copyright (c) {{YEAR}}, {{AUTHOR}}
 */
class {{CLASS_NAME}} /* TODO: change class name (from plural to singular number for example) */ extends \Asymptix\db\DBObject {
    const TABLE_NAME = "{{TABLE_NAME}}";
    const ID_FIELD_NAME = "{{PRIMARY_KEY}}";
    protected $fieldsList = [
{{FIELDS_LIST}}
    ];

    protected $fieldsAliases = []; // TODO: fill fields aliases if needed

}
