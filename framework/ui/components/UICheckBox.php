<?php

require_once("/core/ui/UIComponent.php");

/**
 * CheckBox UI component class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UICheckBox extends UIComponent {
    /**
     * Default checkbox HTML template.
     */
    const DEFAULT_TEMPLATE = "core/ui/templates/components/ui_checkbox.tpl.php";

    /**
     * Makes the select field focused on page load (empty or 'autofocus', optional).
     *
     * @var string
     */
    protected $autofocus = "";

    /**
     * Specifies that the option should be disabled when it first loads
     *           (empty or 'disabled', optional).
     *
     * @var string
     */
    protected $disabled = "";

    /**
     * Defines one ore more forms the select field belongs to (optional).
     *
     * @var string
     */
    protected $form = "";

    /**
     * Defines the value of the option to be sent to the server.
     *
     * @var string,integer
     */
    protected $value = "";

    /**
     * Indicates that the input element should be checked when it first loads.
     *
     * @var boolean
     */
    protected $checked = false;

    /**
     * Generate HTML of the list <input type="checkbox" ... /> element.
     *
     * @param array<string => string> $attributesList List of the component attributes.
     * @param string $template Path to the components template file.
     */
    public function UICheckBox($attributesList, $template) {
        parent::UIComponent($attributesList, $template);
    }
}

?>