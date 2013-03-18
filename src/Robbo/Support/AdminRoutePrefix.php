<?php namespace Robbo\Support;

abstract class RoutePrefix implements \XenForo_Route_Interface {

	protected $_controller;

	protected $_id;

	protected $_group;

	protected $_title = 'title';

	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		if (is_null($this->_controller))
		{
			throw new \XenForo_Exception(get_class($this).' must define $_controller or overwrite match');
		}

		$action = $routePath;
		if ( ! is_null($this->_id))
		{
			$action = $router->resolveActionWithIntegerParam($routePath, $request, $this->_id);
		}

		return $router->getRouteMatch($this->_controller, $action, $this->_group);
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		if (is_null($this->_id))
		{
			return null;
			//return XenForo_Link::buildLink($action); // TODO: test this?
		}

		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, $this->_id, $this->_title);
	}
}