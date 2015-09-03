<?php

require_once("/core/ui/UIComponent.php");
require_once("/core/ui/components/UIListOption.php");

/**
 * DropBox UI component class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UIDropDown extends UIComponent {
    /**
     * Default drop-down list HTML template.
     */
    const DEFAULT_TEMPLATE = "classes/ui_/_templates/components/ui_dropdown.tpl.php";

    /**
     * Makes the select field focused on page load (empty or 'autofocus', optional).
     *
     * @var string
     */
    private $autofocus = "";

    /**
     * When true (not empty), it disables the drop-down list (empty or 'disabled',
     *           optional).
     *
     * @var string
     */
    private $disabled = "";

    /**
     * Defines one ore more forms the select field belongs to (optional).
     *
     * @var string
     */
    private $form = "";

    /**
     * Generate HTML of the list <select> element for drop-down list.
     *
     * @param array<mixed> $dataSet List of drop-down options data.
     * @param string,integer $currentValue Current value if selected.
     * @param array<string => string> $attributesList List of the HTML attributes
     *           for the drop-down element (optional).
     * @param string $template Path to the template file of the drop-down (optional).
     */
    public function UIDropDown($dataSet = array(), $currentValue = null, $attributesList = array(), $template = "") {
        if (empty($template)) {
            $template = self::DEFAULT_TEMPLATE;
        }
        parent::UIComponent($attributesList, $template, $dataSet, $currentValue);
    }
}

?>
