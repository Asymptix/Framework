<?php

namespace Asymptix\ui;

/**
 * Basic UI Control class.
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
abstract class UIControl extends UIComponent
{
    /**
     * Tip text or message for the control.
     *
     * @var string
     */
    protected $tip = '';

    /**
     * Set if control is scrollable or not.
     *
     * @var bool
     */
    protected $scrollable = false;
}
