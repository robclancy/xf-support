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

	public function string()
	{
		return $this->_addDefinition('type', DW::TYPE_STRING)
	}

	public function uinteger()
	{
		return $this->_addDefinition('type', DW::TYPE_UINT);
	}

	public function uint()
	{
		return $this->uinteger();
	}

	public function required()
	{
		return $this->_addDefinition('required', true);
	}

	public function default($value)
	{
		return $this->_addDefinition('default', $value);
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
}