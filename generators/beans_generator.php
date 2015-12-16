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
        $query1 = new DBPreparedQuery("SHOW FULL COLUMNS FROM " . $tableName);
        $stmt1 = $query1->go();
        if ($stmt1 !== false) {
            $stmt1->bind_result($field, $type, $collation, $null, $key, $default, $extra, $privileges, $comment);

            while ($stmt1->fetch()) {
                if ($key === 'PRI') {
                    $idFieldName = $field;
                }
                $extra = trim($extra);
                $comment = trim($comment);

                $fieldsListStr.= "        '" . $field . "' => ";
                if (strpos($type, "varchar") === 0
                 || strpos($type, "text") === 0
                 || strpos($type, "longtext") === 0
                 || strpos($type, "enum") === 0
                 || strpos($type, "char") === 0
                 || strpos($type, "datetime") === 0
                 || strpos($type, "timestamp") === 0
                 || strpos($type, "date") === 0) {
                    $fieldsListStr.= '"' . $default . '"';
                } elseif (strpos($type, "int") === 0
                 || strpos($type, "tinyint") === 0
                 || strpos($type, "smallint") === 0
                 || strpos($type, "mediumint") === 0
                 || strpos($type, "bigint") === 0) {
                    if (!empty($default)) {
                        $fieldsListStr.= $default;
                    } else {
                        $fieldsListStr.= 0;
                    }
                } elseif (strpos($type, "float") === 0
                 || strpos($type, "double") === 0
                 || strpos($type, "decimal") === 0) {
                    if (!empty($default)) {
                        $fieldsListStr.= $default;
                    } else {
                        $fieldsListStr.= "0.0";
                    }
                }
                $fieldsListStr.= ", // " . $type .
                    ", " . (($null == "NO")?"not null":"null")
                    . ", default '" . $default ."'" .
                    ($extra?", " . $extra:"") .
                    ($comment?" (" . $comment . ")":"") . "\n";
            }
            $fieldsListStr = substr($fieldsListStr, 0, strlen($fieldsListStr) - 1);

            $stmt1->close();

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