<?php

namespace Asymptix\app;

/**
 * Application class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class Application
{

    /**
     * Current application controller object.
     *
     * @var Controller
     */
    public static $controller = null;

    /**
     * Current application view object.
     *
     * @var View
     */
    public static $view = null;

}
