<?php namespace Robbo\Support;

use Robbo\DbConnector\Connector;
use Robbo\SchemaBuilder\Connection;

abstract class Installer {

	protected $schema;

	protected $connection;

	protected $data;

	protected $existingData;

	protected $upLog;

	public static function install(array $existingAddOn, array $addOnData)
	{
		return (new static)->up($existingAddOn, $addOnData);
	}

	public static function uninstall(array $existingData)
	{
		return (new static)->down($existingAddOn);
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

	public function up(array $existingAddOn, array $addOnData)
	{
		$this->existingData = $existingAddOn;
		$this->data = $addOnData;

		$this->_setUp();

		try 
		{
			$this->_runMethods('up', range($existingAddOn['version_id']+1, $addOnData['version_id']));
		}
		catch (Exception $e)
		{
			// Run down on everything we just did up on
			$this->_runMethods('_down', $this->upLog);

			throw $e;
		}
	}

	public function down(array $existingAddOn)
	{
		$this->existingData = $existingAddOn;

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