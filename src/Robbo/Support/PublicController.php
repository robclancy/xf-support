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
	const JSON_ARRAY = 'json_array';
	const DATE_TIME  = 'dateTime';
	const ARRAY_SIMPLE = 'array_simple';

	protected $_dataModel;

	protected $_repository;

	protected $_id;

	protected function _preDispatch($action)
	{
		if ( ! is_null($this->_dataModelName))
		{
			$this->_dataModel = $this->getModelFromCache($this->_getDataModelName());
			$repoName = $this->_getRepositoryName();
			$this->_repository = new $repoName($this->_dataModel);
		
			$this->_id = $this->_input->filterSingle($this->_dataModel->getKey(), self::UINT);
		}

		parent::_preDispatch($action);
	}

	abstract protected function _getDataModelName();

	protected function _getRepositoryName()
	{
		return 'Robbo\Support\Repository';
	}
}