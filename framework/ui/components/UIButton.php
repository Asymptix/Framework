<?php

namespace Asymptix\ui\components;

/**
 * UI Button component class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UIButton extends \Asymptix\ui\UIComponent {

    const DEFAULT_TEMPLATE = "/../templates/components/ui_button.tpl.php";

    const BTN_RESET = "reset";
    const BTN_BUTTON = "button";
    const BTN_SUBMIT = "submit";

    protected $type = self::BTN_BUTTON;

    protected $isClose = false;

    public function __construct($attribs = array(), $template = null, $show = false) {
        if (empty($template)) {
            $template = __DIR__ . self::DEFAULT_TEMPLATE;
        }

        if ($show) {
            parent::__construct($attribs, $template);
        } else {
            $this->template = $template;
            $this->setAttributes($attribs);
        }
    }
}