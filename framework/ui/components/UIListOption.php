<?php

namespace Asymptix\ui\components;

/**
 * DropDown List UI component class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UIListOption extends \Asymptix\ui\UIComponent {
    /**
     * Default drop-down list HTML template.
     */
    const DEFAULT_TEMPLATE = "core/ui/templates/components/ui_listoption.tpl.php";

    /**
     * Specifies that the option should be disabled when it first loads
     *           (empty or 'disabled', optional).
     *
     * @var string
     */
    public $disabled = "";

    /**
     * Defines a label to use when using <optgroup>.
     *
     * @var string
     */
    public $label = "";

    /**
     * Defines the value of the option to be sent to the server.
     *
     * @var string,integer
     */
    protected $value = "";

    /**
     * Generate HTML of the list <option> element.
     *
     * @param string,integer $optionValue Value of the option.
     * @param mixed $option Option object, pair array or single title value.
     * @param string,integer $currentValue Current selected list option value (optional).
     * @param string $template HTML template of this component (optional).
     */
    public function __construct($optionValue, $option, $currentValue, $template = "") {
        if (empty($template)) {
            $template = self::DEFAULT_TEMPLATE;
        }
        if (isObject($option)) {
            parent::__construct(
                [
                    'value' => $option->id,
                    'title' => $option->title
                ],
                $template,
                [],
                $currentValue
            );
        } elseif (is_array($option)) {
            parent::__construct(
                $option,
                $template,
                [],
                $currentValue
            );
        } else {
            parent::__construct(
                [
                    'value' => $optionValue,
                    'title' => $option
                ],
                $template,
                [],
                $currentValue
            );
        }
    }
}
