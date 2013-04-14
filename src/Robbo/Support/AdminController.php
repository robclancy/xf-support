<?php namespace Robbo\Support;

abstract class AdminController extends \XenForo_ControllerAdmin_Abstract {

	// These are added here so we don't have to type out \XenForo_Input:: over and over
	const STRING     = 'string';
	const NUM        = 'num';
	const UNUM       = 'unum';
	const INT        = 'int';
	const UINT       = 'uint';
	const FLOAT      = 'float';
	const BINARY     = 'binary';
	const JSON_ARRAY = 'json_array';
	const DATE_TIME  = 'dateTime';
	const ARRAY_SIMPLE = 'array_simple';

	protected $_dataModel;

	protected $_dataModelName;

	protected $_repository;

	protected $_repositoryName = 'Robbo\Support\Repository';

	protected $_idName;

	protected $_id;

	protected $_adminPermission;

	protected function _preDispatch($action)
	{
		if ( ! is_null($this->_adminPermission))
		{
			$this->assertAdminPermission($this->_adminPermission);
		}

		if ( ! is_null($this->_dataModelName))
		{
			$this->_dataModel = $this->getModelFromCache($this->_dataModelName);
			$this->_repository = new $this->_repositoryName($this->_dataModel);
		}

		if ( ! is_null($this->_idName))
		{
			$this->_id = $this->_input->filterSingle($this->_idName, self::UINT);
		}

		parent::_preDispatch($action);
	}
}