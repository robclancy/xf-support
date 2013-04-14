<?php namespace Robbo\Support;

abstract class DataModel extends \XenForo_Model implements DataModelInterface {

	protected $_defaultOrder = '';

	public function __construct()
	{
		// Populate $this->_db so we can just use that instead
		$this->_getDb();

		$this->_setTableAndKey();
	}

	protected function _setTableAndKey()
	{
		$writer = $this->_getWriterName();
		$this->_table = $writer::getTable();
		$this->_key = $writer::getKey();
	}

	public static function getKey()
	{
		$writer = $this->_getWriterName();
		return $writer::getKey();
	}

	public static function getTable()
	{
		$writer = $this->_getWriterName();
		return $writer::getTable();
	}

	abstract protected function _getWriterName();

	public function getNewWriter()
	{
		return  \XenForo_DataWriter::create($this->_getWriterName());
	}

	public function getResourceById($id, array $fetchOptions = array())
	{
		$joinOptions = $this->prepareResourceFetchOptions($fetchOptions);

		return $this->_db->fetchRow('
			SELECT '.$this->_table.'.*
				'.$joinOptions['selectFields'].'
			FROM '.$this->_table.'
			'.$joinOptions['joinTables'].'
			WHERE '.$this->_key.' = ?
		', $id);
	}

	public function getResources(array $conditions = array(), array $fetchOptions = array())
	{
		$whereClause = $this->prepareResourceConditions($conditions, $fetchOptions);

		$orderClause = $this->prepareResourceOrderOptions($fetchOptions, $this->_defaultOrder);
		$joinOptions = $this->prepareResourceFetchOptions($fetchOptions);
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		return $this->fetchAllKeyed($this->limitQueryResults(
			'
				SELECT '.$this->_table.'.*
					'.$joinOptions['selectFields'].'
				FROM '.$this->_table.'
				'.$joinOptions['joinTables'].'
				WHERE ' . $whereClause . '
				'.$orderClause.'
			', $limitOptions['limit'], $limitOptions['offset']
		), $this->_key);
	}

	public function getAllResources(array $fetchOptions = array())
	{
		$joinOptions = $this->prepareResourceFetchOptions($fetchOptions);
		$orderClause = $this->prepareResourceOrderOptions($fetchOptions, $this->_defaultOrder);

		return $this->fetchAllKeyed('
			SELECT '.$this->_table.'.*
				'.$joinOptions['selectFields'].'
			FROM '.$this->_table.'
			'.$joinOptions['joinTables'].'
			'.$orderClause.'
		', $this->_key);
	}

	public function insert(array $data)
	{
		$dw = $this->getNewWriter();

		$dw->bulkSet($data);
		$dw->save();

		return $dw->getMergedData();
	}

	public function update($id, array $data)
	{
		$dw = $this->getNewWriter();

		$dw->setExistingData($id);
		$dw->bulkSet($data);
		$dw->save();

		return $dw->getMergedData();
	}

	public function delete($id)
	{
		$dw = $this->getNewWriter();

		$dw->setExistingData($id);
		
		return $dw->delete();
	}

	public function prepareResourceFetchOptions(array $fetchOptions)
	{	
		return array('selectFields' => '', 'joinTables' => '');
	}

	public function prepareResourceConditions(array $conditions, array &$fetchOptions)
	{
		return '1=1';
	}

	public function prepareResourceOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
	{
		return $defaultOrderSql;
	}
}