<?php
/**
 * Router
 * @author Daniil Mikhailov <info@mdsina.ru>
 * @copyright Copyright (c) 2014, Daniil Mikhailov
 */

namespace App;
use Framework\Di\Di;

/**
 * Class Router
 */
class Router
{

    /**
     * App_Router instance
     *
     * @var App_Router
     */
    protected static $_instance;


    /**
     * @var Di
     */
    protected $_di;


    /**
     * Get App_Router instance
     *
     * @return App_Router
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }


    /**
     * Start routing
     *
     * @param Di $di
     * @throws Framework_Exception_Page
     */
    public function run(Di $di)
	{
        $this->_di = $di;
        $request = $di->get('Request');

		// Initialize default controller and action
		$controllerName = $this->_getControllerName('\App\Controller\NotFound');
		$actionName = $request->getActionName('view');

		// Try to create controller
        try {
            $controller = new $controllerName($di, $request);
        } catch (\Framework_Exception_Class $e) {
            $error = new Controller\NotFound($di);
            $error->view();
            exit();
        }

        if (!$controller->$actionName()) {
            throw new \Framework_Exception_Page(sprintf('OOps, action "%s" not found', $actionName));
        }
	}


    /**
     * Get Controller name from routes map
     *
     * @param null $default
     * @return int|null|string
     */
    private function _getControllerName($default = null)
    {
        require_once($this->_di->get('Params')->getParams('routes_file'));

        $path = $this->_di->get('Request')->getRequest();

        foreach ($routes as $controller => $route) {
            if (in_array($path, $route)) {
                return $controller;
            }
        }

        return $default;
    }
}
