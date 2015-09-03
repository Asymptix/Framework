<?php

require_once("core/ui/UIControl.php");
require_once("core/ui/components/UIButton.php");

/**
 * Dialog UI class controll.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UIDialog extends UIControl {
    /**
     * Default dialog panel HTML template.
     */
    const DEFAULT_TEMPLATE = "core/ui/templates/controls/ui_dialog.tpl.php";

    /**
     * Text of the dialog message.
     */
    protected $text = "";

    /**
     * List of dialog buttons.
     *
     * @var array<UIButton>
     */
    protected $buttons = array();

    public function UIDialog($attributesList = array(), $buttons = array(), $template = "") {
        $this->buttons = $buttons;

        if (empty($template)) {
            $template = self::DEFAULT_TEMPLATE;
        }
        parent::UIComponent($attributesList, $template);
    }

    public function showButtons() {
        foreach ($this->buttons as $btn) {
            $btn->show();
        }
    }

}

?>