<?php

require_once("/core/ui/UIComponent.php");

/**
 * UI Button component class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/dzarezenko/Asymptix-PHP-Framework.git
 * @license http://opensource.org/licenses/MIT
 */
class UIButton extends UIComponent {

    const DEFAULT_TEMPLATE = "core/ui/templates/components/ui_button.tpl.php";

    const BTN_RESET = "reset";
    const BTN_BUTTON = "button";
    const BTN_SUBMIT = "submit";

    protected $type = self::BTN_BUTTON;

    protected $isClose = false;

    public function UIButton($attribs = array(), $template = null, $show = false) {
        if (empty($template)) {
            $template = self::DEFAULT_TEMPLATE;
        }

        if ($show) {
            parent::UIComponent($attribs, $template);
        } else {
            $this->template = $template;
            $this->setAttributes($attribs);
        }
    }

}

?>