<?php

namespace Asymptix\core;

/**
 * Abstract Controller class, parent for all controllers.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
abstract class Controller {

    public $action;
    protected $defaultAction;
    public $tpl;

    public function __construct($action = null) {
        $this->action = $action ?: $this->defaultAction;
    }

    public function render($tpl = null) {
        if (!empty($tpl)) {
            $this->tpl = $tpl;
        }

        require_once($this->tpl);
    }

}