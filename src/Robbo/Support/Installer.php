<?php namespace Robbo\Support;

use Robbo\DbConnector\Connector;
use Robbo\SchemaBuilder\Connection;

abstract class Installer {

	protected $schema;

	protected $connection;

	protected $data;

	protected $existingData;

	protected $upLog;

	public static function install($existingData, array $data)
	{
		return (new static)->up($existingData, $data);
	}

	public static function uninstall(array $existingData)
	{
		return (new static)->down($existingData);
	}

	protected function _setUp()
	{
		$config = \XenForo_Application::getConfig()->toArray();
		$config = $config['db'];

		// XenForo only supports MySQL I think...
		$config['driver'] = 'mysql';

		$pdo = Connector::create($config, true);
		$this->connection = Connection::create($config['driver'], $pdo, $config['dbname']);
		$this->schema = $this->connection->getSchemaBuilder();
	}

	public function up($existingData, array $data)
	{
		$this->existingData = $existingData;
		$this->data = $data;

		$this->_setUp();

		try 
		{
			$existingVersion = $existingData ? $existingData['version_id'] : 0;
			$this->_runMethods('up', range($existingVersion+1, $data['version_id']));
		}
		catch (Exception $e)
		{
			// Run down on everything we just did up on
			$this->_runMethods('_down', $this->upLog);

			throw $e;
		}
	}

	public function down(array $existingData)
	{
		$this->existingData = $existingData;

		$this->_setUp();

		$this->_runMethods('down', range($from, 0));
	}

	protected function _runMethods($method, $versions)
	{
		foreach ($versions AS $i)
		{
			if (method_exists(array($this, $method.$i)))
			{
				$this->{$method.$i}();
				$this->upLog[] = $i;
			}
		}
	}
}