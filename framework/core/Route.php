<?php

namespace Asymptix\core;

use Asymptix\web\Request;

/**
 * Main route functionality class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2011 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Route {

    public $controller = null;
    public $action = null;
    public $id = null;
    public $customFields = array();

    public $isBackend = false;

    /**
     * Constructor that creates Route object from URL request string.
     *
     * @param string $request URL request string without GET params.
     */
    public function __construct($request = "") {
        $route = array_values(array_filter(explode("/", $request)));

        // result array must contain keys starts from 0
        if (isset($route[0]) && $route[0] == "admin") {
            array_shift($route);
            $this->isBackend = true;
        }
        if (count($route) < 3) {
            $route = array_merge($route, array_fill(0, 3 - count($route), ""));
        } else {
            $route = array_values($route);
        }

        list($this->controller, $this->action, $this->id) = $route;

        if (count($route) > 3) {
            $this->customFields[] = array_slice($route, 3);
        }
    }

    /**
     * Detects if current Route controller present in aliases list (used for
     * example for Menu functionality).
     *
     * @param array<string> $aliases List of aliases.
     *
     * @return boolean
     */
    public function isOneOf($aliases) {
        return in_array($this->controller, $aliases);
    }

    /**
     * Detects current controller action.
     * Priority is Request Field value, Route->action field value, $defaultAction.
     *
     * @param string $actionFieldName $_REQUEST action field name if form was
     *           submitted (default: 'action').
     * @param string $defaultAction Default action if no action detected
     *           (default: 'list').
     *
     * @return string Action name.
     */
    public function getAction($actionFieldName = 'action', $defaultAction = 'list') {
        $action = Request::getFieldValue($actionFieldName);
        if (empty($action)) {
            if (!empty($this->action)) {
                $action = $this->action;
            } else {
                $action = $defaultAction;
            }
        }
        $this->action = $action;

        return $this->action;
    }

    /**
     * Generates URL for current Route with action, id and GET params.
     *
     * @param string $action Action.
     * @param mixed $id Id of the record (optional).
     * @param array $getParams List of GET params (optional).
     *
     * @return string URL.
     */
    public function getUrl($action, $id = null, $getParams = array()) {
        $url = $this->controller . "/";
        if (!is_null($id)) {
            $url.= $action . "/" . $id;
        } else {
            $url.= $action;
        }

        if (!empty($getParams)) {
            $url.= "?";
            foreach ($getParams as $key => $value) {
                $url.= $key . "=" . $value;
            }
        }
        return $url;
    }

    /**
     * Generates default template path for current route.
     *
     * @return string
     */
    public function tplPath() {
        $path = $this->controller . "/" . $this->controller;
        if (!empty($this->action)) {
            $path.= "_" . $this->action;
        }

        return $path;
    }

}