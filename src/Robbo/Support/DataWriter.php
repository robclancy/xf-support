<?php namespace Robbo\Support;

abstract class DataWriter extends \XenForo_DataWriter {

	protected $_dataWriteFields = array();

	protected $_key;

	protected $_table;

	protected $_model;

	abstract protected function _setFields();

	protected function _getFields()
	{
		if (empty($this->_dataWriteFields))
		{
			$this->_setFields();
		}

		$fields = array();
		foreach ($this->_dataWriteFields AS $field)
		{
			$fields[$field->getTable()][$field->getName()] = $field->toArray();
		}

		return $fields;
	}

	protected function _field($name, $table = null)
	{
		return new DataWriterField($name, $table ? $table : $this->_table);
	}

	protected function _genericExistingData($table, $key, RepositoryInterface $repository, $data)
	{
		if ( ! $id = $this->_getExistingPrimaryKey($data, $key))
		{
			return false;
		}

		return array($table => $repository->getById($id));
	}

	protected function _genericUpdateCondition($table, $key)
	{
		// TODO: stuff with table here?

		return $key.' = '.$this->_db->quote($this->getExisting($key));
	}

	protected function _createRepository(DataModelInterface $model)
	{
		return new Repository($model);
	}
}