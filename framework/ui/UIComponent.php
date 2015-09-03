<?php

/**
 * Basic high level primitive class of all UI components and controls.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2015, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
abstract class UIComponent {
    /**
     * Path to the components default template file.
     */
    const DEFAULT_TEMPLATE = "";

    /**
     * Specifies a unique id for an element.
     *
     * @var string
     */
    protected $id = "";

    /**
     * Name of the HTML element.
     *
     * @var string
     */
    protected $name = "";

    /**
     * Specifies a classname for an element (used to specify a class in a style
     * sheet).
     *
     * @var array<string>
     */
    protected $class = array();

    /**
     * Specifies a set of CSS style pairs for style attribute of the HTML element.
     *
     * @var array<string, string>
     */
    protected $style = array();

    /**
     * Specifies that the element is not relevant. Hidden elements are not
     * displayed (empty or 'hidden').
     *
     * @var string
     */
    protected $hidden = "";

    /**
     * Specifies extra information about an element.
     *
     * @var string
     */
    protected $title = "";

    /**
     * Dataset for the component.
     *
     * @var array
     */
    protected $dataSet = array();

    /**
     * Current value for the component.
     *
     * @var mixed
     */
    protected $currentValue;

    /**
     * Path to the components template file.
     *
     * @var string
     */
    protected $template = null;

    /**
     * Constructor of the class.
     *
     * @param array<string => string> $attributesList List of the component attributes.
     * @param string $template Path to the components template file.
     * @param <type> $dataSet
     * @param <type> $currentValue
     */
    protected function UIComponent($attributesList = array(), $template = "", $dataSet = array(), $currentValue = null) {
        $this->setAttributes($attributesList);
        $this->dataSet = $dataSet;
        $this->currentValue = $currentValue;
        $this->template = $template;

        $this->show();
    }

    /**
     * Set HTML-element attributes if exists. Throws exception if attribute
     * doesn't exist.
     *
     * @param array<string => mixed> $attributesList List of the attributes.
     */
    protected function setAttributes($attributesList) {
        //var_dump($this);
        foreach ($attributesList as $attributeName => $attributeValue) {
            if (isset($this->$attributeName)) {
                $this->$attributeName = $attributeValue;
            } else {
                throw new Exception("Wrong attribute '" . $attributeName . "' for " . get_class($this) . " component");
            }
        }
    }

    /**
     * Generate element HTML-code by template.
     *
     * @return nothing
     */
    protected function show() {
        if (!empty($this->template)) {
            include($this->template);
            return;
        }
        include(self::DEFAULT_TEMPLATE);
    }

    /**
     * css($propertyName) Get the value of a CSS property;
     * css($propertyName, "") Unset a CSS property;
     * css($propertyName, $propertyValue) Set the value of a CSS property
     *
     * @param string $propertyName Name of a CSS property.
     * @param mixed $propertyValue Value of a CSS property.
     *
     * @return Value of the CSS property or NULL if property is not exists.
     */
    public function css($propertyName, $propertyValue = null) {
        if (is_null($propertyValue)) {
            if (isset($this->style[$propertyName])) {
                return $this->style[$propertyName];
            }
            return null;
        } else {
            if ($propertyValue === "") {
                if (isset($this->style[$propertyName])) {
                    unset($this->style[$propertyName]);
                }
            } else {
                $this->style[$propertyName] = $propertyValue;
            }
        }
    }

    public function getStyle() {
        $style = "";
        foreach ($this->style as $propertyName => $propertyValue) {
            $style .= $propertyName . ":" . $propertyValue . ";";
        }
        return $style;
    }

    public function setStyle($style) {
        $this->style = array();
        $stylesList = array_filter(explode(";", $style));
        foreach ($stylesList as $style) {
            $stylePair = explode(":", $style);
            if (isset($stylePair[0]) && isset($stylePair[1])) {
                $propertyName = trim($stylePair[0]);
                $propertyValue = trim($stylePair[1]);
                $this->css($propertyName, $propertyValue);
            } else {
                throw new Exception("Invalid CSS style string"); //todo: write own exception
            }
        }
    }

    /**
     * Determine whether element is assigned the given class
     *
     * @param string $className Name of the class
     *
     * @return boolean
     */
    public function hasClass($className) {
        return (array_search($className, $this->class) !== false);
    }

    public function addClass($className) {
        $this->class[] = $className;
        $this->class = array_unique($this->class);
    }

    public function removeClass($className = null) {
        if (is_null($className)) {
            $this->class = array();
        } else {
            $key = array_search($className, $this->class);
            if ($key !== false) {
                unset($this->class[$key]);
            }
        }
    }

    public function getClass() {
        return implode(" ", $this->class);
    }

    public function setClass($class) {
        $this->class = array();
        $class = preg_replace("#( ){2,}#", " ", $class);
        $classesList = array_filter(explode(" ", $class));
        foreach ($classesList as $className) {
            $this->addClass($className);
        }
    }

}

?>
