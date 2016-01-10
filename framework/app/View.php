<?php

namespace Asymptix\core;

/**
 * View class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class View {

    private $_route = null;
    private $_tpl = null;

    private $_fields = array();
    private $_messages = array();
    private $_errors = array();

    public function __construct($tpl) {
        if (!empty($tpl)) {
            if (is_string($tpl)) {
                $this->_tpl = $tpl;
            } else if (Tools::isInstanceOf($tpl, new Route)) {
                $this->_route = $tpl;
                $this->_tpl = $this->_route->controller . "/" . $this->_route->controller . "_" . $this->_route->action;
            } else {
                throw new \Exception("Invalid view template");
            }
        } else {
            throw new \Exception("Empty view template");
        }
    }

    public function render($path = "", $suffix = "") {
        if (empty($this->_tpl)) {
            throw new \Exception("View object was not initialized with template");
        }

        require_once($path . $this->_tpl . $suffix);
    }

    function setTpl($_tpl) {
        $this->_tpl = $_tpl;
    }

    public function getFields() {
        return $this->_fields;
    }

    public function setFields($_fields) {
        $this->_fields = $_fields;
    }

    public function getMessages() {
        return $this->_messages;
    }

    public function setMessages($_messages) {
        $this->_messages = $_messages;
    }

    public function getErrors() {
        return $this->_errors;
    }

    public function setErrors($_errors) {
        $this->_errors = $_errors;
    }

}