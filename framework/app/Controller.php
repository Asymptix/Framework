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

    public $route = null;
    protected $defaultAction = "";
    public $view = null;

    public function __construct($route = null) {
        $this->route = $route;
        $this->route->getAction('action', $this->defaultAction);

        $this->view = new View($this->route);

        $this->init();
    }

    protected function init() {
        $action = 'action' . ucfirst($this->route->action);
        return $this->$action();
    }

    public function getDefaultAction() {
        return $this->defaultAction;
    }

}