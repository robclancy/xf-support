<?php namespace Robbo\Support;

abstract class DataModel extends \XenForo_Model implements DataModelInterface {

	protected $table;

	protected $key;

	public function __construct()
	{
		// Populate $this->_db so we can just use that instead
		$this->_getDb(); 
	}

	public function getResourceById($id, array $fetchOptions = array())
	{
		$this->assertTableAndKeySet();

		$joinOptions = $this->getResourceJoinOptions($fetchOptions);

		return $this->_db->fetchRow('
			SELECT '.$this->_table.'.*
				'.$joinOptions['selectFields'].'
			FROM '.$this->_table.'
				'.$joinOptions['joinTables'].'
			WHERE '.$this->_key.' = ?
		', $id);
	}

	public function getResourceJoinOptions(array $fetchOptions)
	{	
		return array('selectFields' => '', 'joinTables' => '');
	}

	protected function assertTableAndKeySet()
	{
		if (is_null($this->table) OR is_null($this->key)
		{
			throw new \XenForo_Exception(__CLASS__.'->_table and '.__CLASS__.'->_key must be set in '.__FILE__);
		}
	}
}