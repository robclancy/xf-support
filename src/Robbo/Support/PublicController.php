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

	protected $dataModel;

	protected $dataModelName;

	protected $repository;

	protected $repositoryName = 'Robbo\Support\Repository';

	protected function _preDispatchType($action)
	{
		if ( ! is_null($this->dataModelName))
		{
			$this->dataModel = $this->getModelFromCache($this->dataModelName);
			$this->repository = new $this->repositoryName($this->dataModel);
		}

		parent::_preDispatchType($action);
	}
}