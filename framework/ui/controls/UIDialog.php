<?php

namespace Asymptix\ui\controls;

/**
 * Dialog UI class controll.
 *
 * @category Asymptix PHP Framework
 *
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 *
 * @license http://opensource.org/licenses/MIT
 */
class UIDialog extends \Asymptix\ui\UIControl
{
    /**
     * Default dialog panel HTML template.
     */
    const DEFAULT_TEMPLATE = '/../templates/controls/ui_dialog.tpl.php';

    /**
     * Text of the dialog message.
     */
    protected $text = '';

    /**
     * List of dialog buttons.
     *
     * @var array<UIButton>
     */
    protected $buttons = [];

    public function __construct($attributesList = [], $buttons = [], $template = '')
    {
        $this->buttons = $buttons;

        if (empty($template)) {
            $template = __DIR__.self::DEFAULT_TEMPLATE;
        }
        parent::__construct($attributesList, $template);
    }

    public function showButtons()
    {
        foreach ($this->buttons as $btn) {
            $btn->show();
        }
    }
}
