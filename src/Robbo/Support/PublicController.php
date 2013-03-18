<?php namespace Robbo\Support;

abstract class PublicController extends \XenForo_ControllerPublic_Abstract {

	// These are added here so we don't have to type out \XenForo_Input:: over and over
	const STRING     = 'string';
	const NUM        = 'num';
	const UNUM       = 'unum';
	const INT        = 'int';
	const UINT       = 'uint';
	const FLOAT      = 'float';
	const BINARY     = 'binary';
	const ARRAY_SIMPLE = 'array_simple';
	const JSON_ARRAY = 'json_array';
	const DATE_TIME       = 'dateTime';

	protected $_dataModel;

	protected $_dataModelName;

	protected $_repository;

	protected $_repositoryName = 'Robbo\Support\Repository';

	protected $_idName;

	protected $_id;

	protected function _preDispatchType($action)
	{
		if ( ! is_null($this->_dataModelName))
		{
			$this->_dataModel = $this->getModelFromCache($this->_dataModelName);
			$this->_repository = new $this->_repositoryName($this->_dataModel);
		}

		if ( ! is_null($this->_idName))
		{
			$this->_id = $this->_input->filterSingle($this->_idName, self::UINT);
		}

		parent::_preDispatchType($action);
	}
}