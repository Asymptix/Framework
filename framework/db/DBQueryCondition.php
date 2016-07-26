<?php

namespace Asymptix\db;

use Asymptix\core\Tools;

/**
 * DB SQL query condition class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class DBQueryCondition {
    /**
     * Database condition field.
     *
     * @var DBField
     */
    public $field;

    /**
     * Type of condition.
     *
     * @var string
     */
    public $type;

    /**
     * Condition value
     *
     * @var mixed
     */
    public $value;

    /**
     * Result SQL condition string
     *
     * @var string
     */
    private $sqlCondition;

    /**
     * Result prepare statement SQL condition string
     *
     * @var string
     */
    private $preparedCondition = "";

    /**
     * Types string for prepared statement
     *
     * @var string
     */
    private $preparedTypes = "";

    /**
     * List with data for prepared statement
     *
     * @var array<mixed>
     */
    private $preparedData = [];

    /**
     * Inits DBQueryCondition object.
     *
     * @param DBField $field Field to compare.
     * @param string $conditionType Type of the comparison operator or condition.
     * @param mixed $value May be other DBField object or value to compare with.
     *
     * @throws DBQueryConditionException If some parameters invalid.
     */
    public function __construct(DBField $field, $conditionType, $value) {
        $this->type = self::sqlConditionType($conditionType);
        if (!Tools::isInstanceOf($value, "DBField")) {
            $this->field = $field;

            switch ($this->type) {
                case ("="):
                case ("<"):
                case (">"):
                case ("!="):
                    $this->value = DBField::castValue($this->field->type, $value);

                    $this->sqlCondition = "`" . $field->name . "` " . $this->type . " " . DBField::sqlValue($this->field->type, $value);

                    $this->preparedCondition = "`" . $field->name . "` " . $this->type . " ?";
                    $this->preparedTypes = $this->field->type;
                    $this->preparedData = [DBField::sqlValue($this->field->type, $value)];
                    break;
                case ("LIKE"):
                case ("NOT LIKE"):
                    $this->value = DBField::castValue($this->field->type, $value);

                    if ($this->field->type != "s") {
                        throw new DBQueryConditionException("Field type is not a string");
                    }
                    $this->sqlCondition = "`" . $field->name . "` " . $this->type . " " . DBField::sqlValue($this->field->type, $value);

                    $this->preparedCondition = "`" . $field->name . "` " . $this->type . " ?";
                    $this->preparedTypes = $this->field->type;
                    $this->preparedData = [DBField::sqlValue($this->field->type, $value)];
                    break;
                case ("IN"):
                case ("NOT IN"):
                    if (is_array($value) &&  !empty($value)) {
                        $dataList = [];
                        foreach ($value as $dataItem) {
                            $dataList[] = DBField::sqlValue($this->field->type, $dataItem);
                        }
                        $dataList = array_unique($dataList);
                        $count = count($dataList);
                        if ($count > 0) {
                            $qmStr = "?";
                            $tStr = $this->field->type;
                            for ($i = 1; $i < $count; $i ++) {
                                $qmStr .= ", ?";
                                $tStr .= $this->field->type;
                            }
                        } else {
                            $this->sqlCondition = "1";

                            return;
                        }

                        $this->sqlCondition = "`" . $field->name . "` " . $this->type . " (" . join(", ", $dataList) . ")";

                        $this->preparedCondition = "`" . $field->name . "` " . $this->type . " (" . $qmStr . ")";
                        $this->preparedTypes = $tStr;
                        $this->preparedData = $dataList;
                    } else {
                        throw new DBQueryConditionException("Invalid data for 'IN'/'NOT IN' condition");
                    }
                    break;
                case ("BETWEEN"):
                    if (is_array($value) && count($value) == 2 && isset($value[0]) && isset($value[1])) {
                        $from = DBField::sqlValue($this->field->type, $value[0]);
                        $to = DBField::sqlValue($this->field->type, $value[1]);
                        $this->sqlCondition = "`" . $field->name . "` BETWEEN " . $from . " AND " . $to;

                        $this->preparedCondition = "`" . $field->name . "` BETWEEN ? AND ?";
                        $this->preparedTypes = $this->field->type . $this->field->type;
                        $this->preparedData = [$from, $to];
                    } else {
                        throw new DBQueryConditionException("Invalid data for 'BETWEEN' condition");
                    }
                    break;
            }
        } else {
            $field1 = $field;
            $field2 = $value;

            switch ($this->type) {
                case ("="):
                case ("<"):
                case (">"):
                case ("!="):
                case ("LIKE"):
                case ("NOT LIKE"):
                    $this->sqlCondition = "`" . $field1->name . "` " . $this->type . " `" . $field2->name . "`";
                    break;
                case ("IN"):
                case ("NOT IN"):
                    // impossible, use array instead of DBField
                    break;
                case ("BETWEEN"):
                    // impossible, use array instead of DBField
                    break;
            }
        }
    }

    /**
     * Generates SQL formatted condition string.
     *
     * @param mixed $queryCondition List of DBQueryCondition objects or object
     *           itself.
     * @param string $operator Initial logical OR or AND operator.
     *
     * @return string SQL query condition string.
     */
    public static function getSQLCondition($queryCondition, $operator = "") {
        $operator = strtoupper(trim($operator));
        if ($operator === "OR" || $operator === "AND") {
            if (is_array($queryCondition)) {
                if ($operator === "AND") {
                    $cond = " (1";
                } else {
                    $cond = " (0";
                }

                foreach ($queryCondition as $operation => $conditions) {
                    $cond .= " " . $operator . self::getSQLCondition($conditions, $operation);
                }

                $cond .= ")";
                return $cond;
            }
        } else {
            if (is_array($queryCondition)) {
                foreach ($queryCondition as $operation => $conditions) {
                    return trim(
                        str_replace(["(1 AND ", "(0 OR "], "(",
                            self::getSQLCondition($conditions, $operation)
                        )
                    );
                }
            } elseif (Tools::isInstanceOf($queryCondition, "\Asymptix\db\DBQueryCondition")) {
                return (" " . $queryCondition->sqlCondition);
            }
            return "";
        }
    }

    /**
     * Normalize query condition operator (type).
     *
     * @param string $conditionType Condition type.
     *
     * @return string Normalized condition type.
     * @throws DBQueryConditionException If invalid type provided.
     */
    private static function sqlConditionType($conditionType) {
        $conditionType = preg_replace("#[[:blank:]]{2,}#", " ", strtolower(trim($conditionType)));

        $conditionTypes = [
            // Equal operator
            '=' => "=",
            'eq' => "=",
            'equal' => "=",

            // Not equal operator
            '!=' => "!=",
            '<>' => "!=",
            'neq' => "!=",
            'not equal' => "!=",

            // Less than operator
            '<' => "<",
            'lt' => "<",
            'less than' => "<",

            // Greater than operator
            '>' => ">",
            'gt' => ">",
            'greater than' => ">",

            // Check whether a value is within a set of values
            'in' => "IN",

            // Check whether a value is not within a set of values
            'not in' => "NOT IN",

            // Simple pattern matching
            'like' => "LIKE",
            'match' => "LIKE",

            // Negation of simple pattern matching
            'not like' => "NOT LIKE",
            'not match' => "NOT LIKE",

            // Check whether a value is within a range of values
            'between' => "BETWEEN"

            //TODO: add all conditions from http://dev.mysql.com/doc/refman/5.0/en/comparison-operators.html
        ];

        if (isset($conditionTypes[$conditionType])) {
            return $conditionTypes[$conditionType];
        }
        throw new DBQueryConditionException("Invalid SQL condition type '" . $conditionType . "'");
    }
}

/**
 * Service exception class.
 */
class DBQueryConditionException extends \Exception {}