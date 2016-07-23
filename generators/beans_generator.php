<?php

require_once("../modules/autoload.php");
require_once("../modules/dbconnection.php");

use Asymptix\db\DBCore;
use Asymptix\db\DBPreparedQuery;
use Asymptix\core\OutputStream;

const RESULTS_PATH = "../classes/db/";
const CLASS_TPL = "templates/bean.tpl";
const AUTHOR = "Dmytro Zarezenko";
const EMAIL = "dmytro.zarezenko@gmail.com";

OutputStream::start();

if (!file_exists(RESULTS_PATH) || is_file(RESULTS_PATH)) {
    OutputStream::msg(OutputStream::MSG_ERROR, "Destination directory '" . RESULTS_PATH . "' doesn't exists.");
    OutputStream::close();
    exit();
}

OutputStream::msg(OutputStream::MSG_INFO, "Reading tables list...");
$query = new DBPreparedQuery("SHOW TABLES");
$stmt = $query->go();
if ($stmt !== false) {
    $tpl = file_get_contents(CLASS_TPL);
    while ($resultSet = DBCore::bindResults($stmt)) {
        $tableName = $resultSet['TABLE_NAMES']['Tables_in_' . conf\Config::getDBConfigParam('DBNAME')];

        OutputStream::msg(OutputStream::MSG_INFO, "Reading structure for table '" . $tableName . "'...");

        $idFieldName = 'id';
        $fieldsListStr = "";
        $fieldsList = DBCore::getTableFieldsList($tableName);
        if (!empty($fieldsList)) {
            foreach ($fieldsList as $field => $attributes) {
                if ($attributes['key'] === 'PRI') {
                    $idFieldName = $field;
                }
                $fieldsListStr.= "        " . DBCore::getPrintableFieldString($field, $attributes);
            }
            $fieldsListStr = substr($fieldsListStr, 0, strlen($fieldsListStr) - 1);

            $className = getClassName($tableName);

            $content = str_replace(array(
                '{{CLASS_NAME}}', '{{TABLE_NAME}}', '{{PRIMARY_KEY}}', '{{FIELDS_LIST}}',
                '{{YEAR}}', '{{AUTHOR}}', '{{EMAIL}}'
            ), array(
                $className, $tableName, $idFieldName, $fieldsListStr,
                date("Y"), AUTHOR, EMAIL
            ), $tpl);

            file_put_contents(RESULTS_PATH . $className . ".php", $content);

            OutputStream::msg(OutputStream::MSG_SUCCESS, "Class '" . RESULTS_PATH . $className . ".php' generated.");
        } else {
            OutputStream::msg(OutputStream::MSG_ERROR, "Can't read structure for table '" . $tableName . "'.");
        }
    }

    $stmt->close();
} else {
    OutputStream::msg(OutputStream::MSG_ERROR, "Can't read tables list.");
}
OutputStream::close();

function getClassName($tableName) {
    $underlinesReplaced = preg_replace_callback(
        "/_([a-zA-Z]{1})/",
        function ($matches) {
            return strtoupper($matches[1]);
        },
        $tableName
    );

    return ucfirst($underlinesReplaced);
}
