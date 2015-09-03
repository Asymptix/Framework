<?php

require_once("core/ui/UIControl.php");
require_once("core/ui/components/UICheckBox.php");

/**
 * CheckBox Panel UI control class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UICheckBoxPanel extends UIControl {
    /**
     * Default checkbox panel HTML template.
     */
    const DEFAULT_TEMPLATE = "core/ui/templates/controls/ui_checkboxpanel.tpl.php";

    public function UI_CheckBoxPanel($dataSet = array(), $currentValue = null, $attributesList = array(), $template = "") {
        parent::UI_Component($attributesList, $template, $dataSet, $currentValue);
    }
}

?>