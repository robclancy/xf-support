<?php namespace Robbo\Support;

use Closure;

abstract class RoutePrefix implements \XenForo_Route_Interface {

	protected $_controller;

	protected $_parent;

	protected $_title = 'title';

	public function match($routePath, \Zend_Controller_Request_Http $request, \XenForo_Router $router)
	{
		if (is_null($this->_controller))
		{
			throw new \XenForo_Exception(get_class($this).' must define $_controller or overwrite match');
		}

		$action = $routePath;
		if ( ! is_null($this->_getKey()))
		{
			$action = $router->resolveActionWithIntegerParam($routePath, $request, $this->_getKey());
		}

		return $router->getRouteMatch($this->_controller, $action, $this->_parent);
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		if (is_null($this->_getKey()))
		{
			return null;
			//return XenForo_Link::buildLink($action); // TODO: test this?
		}

		return \XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, $this->_getKey(), $this->_title);
	}

	protected function _getKey()
	{
		$controller = $this->_controller;
		return $controller::getKey();
	}
}