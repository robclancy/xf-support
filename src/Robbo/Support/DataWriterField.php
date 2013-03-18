<?php namespace Robbo\Support;

class DataWriterField {

	protected $_name;

	protected $_definition = array();

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function string()
	{
		$this->_definition['type'] = \XenForo_DataWriter::TYPE_STRING;

		return $this;
	}

	public function required()
	{
		$this->_definition['required'] = true;

		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function toArray()
	{
		return $this->_definition;
	}
}