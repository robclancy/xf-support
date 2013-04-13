<?php

use Mockery as m;

class DataModelTest extends PHPUnit_Framework_TestCase {

	public function testNoTableOrKeyException()
	{
		$model = new DataModelBadStub1;

		$pass = false;
		try { $model->getResourceById(1); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);

		$pass = false;
		try { $model->getResources(); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);

		$pass = false;
		try { $model->getAllResources(); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);
	}	

	public function testNoKeyException()
	{
		$model = new DataModelBadStub2;

		$pass = false;
		try { $model->getResourceById(1); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);

		$pass = false;
		try { $model->getResources(); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);

		$pass = false;
		try { $model->getAllResources(); } catch (XenForo_Exception $e) { $pass = true; }
		$this->assertTrue($pass);
	}

	public function testGetResourceById()
	{
		$model = new DataModelStub;
		$model->_db->shouldReceive('fetchRow')->with('
			SELECT mytable.*
				
			FROM mytable
			
			WHERE mykey = ?
		', 1)->andReturn(array('test'));
		
		$this->assertEquals(array('test'), $model->getResourceById(1));
	}

	public function testGetPreparedResourceById()
	{
		$model = new PreparedDataModelStub;
		$model->_db->shouldReceive('fetchRow')->with('
			SELECT mytable.*
				,test.name
			FROM mytable
			LEFT JOIN test
			WHERE mykey = ?
		', 1)->andReturn(array('test'));
		
		$this->assertEquals(array('test'), $model->getResourceById(1));
	}

	public function testGetResources()
	{
		$model = new DataModelStub;

		$this->assertEquals(array(
			'
				SELECT mytable.*
					
				FROM mytable
				
				WHERE 1=1
				
			:0:69', 'mykey'), $model->getResources());
	}

	public function testPreparedGetResources()
	{
		$model = new PreparedDataModelStub;

		$this->assertEquals(array(
			'
				SELECT mytable.*
					,test.name
				FROM mytable
				LEFT JOIN test
				WHERE name = robbo
				ORDER BY awesome
			:0:69', 'mykey'), $model->getResources());
	}

	public function testGetAllResources()
	{
		$model = new DataModelStub;

		$this->assertEquals(array(
			'
			SELECT mytable.*
				
			FROM mytable
			
			
		', 'mykey'), $model->getAllResources());
	}

	public function testGetAllPreparedResources()
	{
		$model = new PreparedDataModelStub;

		$this->assertEquals(array(
			'
			SELECT mytable.*
				,test.name
			FROM mytable
			LEFT JOIN test
			ORDER BY awesome
		', 'mykey'), $model->getAllResources());
	}

	public function testInsert()
	{
		$model = new DataModelStub;
		$dw = XenForo_DataWriter::$mock = m::mock('Robbo\SupportDataWriter');

		$dw->shouldReceive('bulkSet')->once()->with(array('id' => 1, 'name' => 'Robbo'));
		$dw->shouldReceive('save')->once();
		$dw->shouldReceive('getMergedData')->once()->andReturn(array('id' => 2, 'name' => 'Robbo'));

		$this->assertEquals(array('id' => 2, 'name' => 'Robbo'), $model->insert(array('id' => 1, 'name' => 'Robbo')));
	}

	public function testUpdate()
	{
		$model = new DataModelStub;
		$dw = XenForo_DataWriter::$mock = m::mock('Robbo\SupportDataWriter');

		$dw->shouldReceive('setExistingData')->once()->with(2);
		$dw->shouldReceive('bulkSet')->once()->with(array('name' => 'Robbo'));
		$dw->shouldReceive('save')->once();
		$dw->shouldReceive('getMergedData')->once()->andReturn(array('id' => 2, 'name' => 'Robbo'));

		$this->assertEquals(array('id' => 2, 'name' => 'Robbo'), $model->update(2, array('name' => 'Robbo')));
	}

	public function testDelete()
	{
		$model = new DataModelStub;
		$dw = XenForo_DataWriter::$mock = m::mock('Robbo\SupportDataWriter');

		$dw->shouldReceive('setExistingData')->once()->with(3);
		$dw->shouldReceive('delete')->once()->andReturn(true);

		$this->assertEquals(true, $model->delete(3));
	}
}

class XenForo_Exception extends Exception {}

class XenForo_Model {

	public $_db;

	protected function _getDb()
	{
		if (is_null($this->_db))
		{
			$this->_db = m::mock('Zend_Db_Adapter_Abstract');
		}

		return $this->_db;
	}

	public function fetchAllKeyed($var1, $var2){ return array($var1, $var2); }

	public function limitQueryResults($var1, $var2, $var3) { return $var1.':'.$var2.':'.$var3; }

	public function prepareLimitFetchOptions() { return array('limit' => 0, 'offset' => 69); }
}

class DataModelBadStub1 extends Robbo\Support\DataModel {

}

class DataModelBadStub2 extends Robbo\Support\DataModel {

	protected $_table = 'test';
}

class DataModelStub extends Robbo\Support\DataModel {

	protected $_table = 'mytable';

	protected $_key = 'mykey';

	protected $_dataWriterName = 'DataWriterStub';
}

class PreparedDataModelStub extends DataModelStub {

	public function prepareResourceFetchOptions(array $fetchOptions)
	{	
		return array('selectFields' => ',test.name', 'joinTables' => 'LEFT JOIN test');
	}

	public function prepareResourceConditions(array $conditions, array &$fetchOptions)
	{
		return 'name = robbo';
	}

	public function prepareResourceOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
	{
		return 'ORDER BY awesome';
	}
}

class XenForo_DataWriter {

	public static $mock;

	public static function create($dw)
	{
		return self::$mock;
	}
}