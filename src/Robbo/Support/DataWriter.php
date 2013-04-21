<?php namespace Robbo\Support;

abstract class DataWriter extends \XenForo_DataWriter {

	protected $_dataWriteFields = array();

	protected static $_key;

	protected static $_table;

	abstract protected function _setFields();

	public static function getKey()
	{
		return static::$_key;
	}

	public static function getTable()
	{
		return static::$_table;
	}

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
		return $this->_dataWriteFields[$table.$name] = new DataWriterField($name, $table ? $table : static::$_table);
	}

	abstract protected function _getDataModelName();

	protected function _getDataModel()
	{
		return $this->getModelFromCache($this->_getDataModelName());
	}

	protected function _getExistingData($data)
    {
    	if ( ! $id = $this->_getExistingPrimaryKey($data, static::$_key))
    	{
    		return false;
    	}

    	return array(static::$_table => $this->_getDataModel()->getById($id));
    }

    protected function _getUpdateCondition($tableName)
    {
    	// TODO: stuff with table here?

    	return static::$_key.' = '.$this->_db->quote($this->getExisting(static::$_key));
    }
}
