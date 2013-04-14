<?php namespace Robbo\Support;

use XenForo_DataWriter as DW;

class DataWriterField {

	protected $_name;

	protected $_table;

	protected $_definition = array();

	public function __construct($name, $table)
	{
		$this->_name = $name;
		$this->_table = $table;
	}

	protected function _addDefinition($key, $value)
	{
		$this->_definition[$key] = $value;

		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function toArray()
	{
		return $this->_definition;
	}

	public function __call($method, $args)
	{
		$types = array(
			'uint' 		=> DW::TYPE_UINT,
			'string' 	=> DW::TYPE_STRING,
		);

		if (isset($types[$method]))
		{
			return $this->_addDefinition('type', $types[$method]);
		}

		$youCantHandleTheTruth = array(
			'required', 'autoIncrement'
		);

		if (in_array($method, $youCantHandleTheTruth))
		{
			return $this->_addDefinition($method, true);
		}

		$className = get_class($this);
		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}
}