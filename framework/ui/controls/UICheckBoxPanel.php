<?php

namespace Asymptix\ui\controls;

/**
 * CheckBox Panel UI control class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UICheckBoxPanel extends \Asymptix\ui\UIControl
{
    /**
     * Default checkbox panel HTML template.
     */
    const DEFAULT_TEMPLATE = "core/ui/templates/controls/ui_checkboxpanel.tpl.php";

    public function __construct($dataSet = [], $currentValue = null, $attributesList = [], $template = "") {
        parent::__construct($attributesList, $template, $dataSet, $currentValue);
    }
}
