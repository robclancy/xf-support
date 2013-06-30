<?php namespace Robbo\Support;

use Robbo\DbConnector\Connector;
use Robbo\SchemaBuilder\Connection;

abstract class Installer {

	protected $_schema;

	protected $_connection;

	protected $_data;

	protected $_existingData;

	protected $_upLog;

	protected $_db;

	public function __construct($existingData, array $data = null)
	{
		$this->_existingData = $existingData;
		$this->_data = $data;

		$this->_setUp();
	}

	public static function install($existingData, array $data)
	{
		$instance = new static($existingData, $data);
		return $instance->up();
	}

	public static function uninstall(array $existingData)
	{
		$instance = new static($existingData);
		return $instance->down();
	}

	protected function _setUp()
	{
		$config = \XenForo_Application::getConfig()->toArray();
		$config = $config['db'];

		// XenForo only supports MySQL I think...
		$config['driver'] = 'mysql';

		$config['database'] = $config['dbname'];
		$config['collation'] = 'utf8_general_ci';
		$config['charset'] = 'utf8';

		$pdo = Connector::create($config, true);
		$this->_connection = Connection::create($config['driver'], $pdo, $config['database']);
		$this->_schema = $this->_connection->getSchemaBuilder();

		$this->_db = \XenForo_Application::getDb();
	}

	public function up()
	{
		try 
		{
			$existingVersion = $this->_existingData ? $this->_existingData['version_id'] : 0;
			$this->_runMethods('up', range($existingVersion+1, $this->_data['version_id']));
		}
		catch (Exception $e)
		{
			// Run down on everything we just did up on
			$this->_runMethods('_down', $this->_upLog);

			throw $e;
		}
	}

	public function down()
	{
		$this->_runMethods('down', range($from, 0));
	}

	protected function _runMethods($method, $versions)
	{
		foreach ($versions AS $i)
		{
			if (method_exists($this, $method.$i))
			{
				$this->{$method.$i}();
				$this->_upLog[] = $i;
			}
		}
	}

	protected function _insertContentTypes($contentTypes)
	{
		$sql = '
			INSERT IGNORE INTO xf_content_type
				(content_type, addon_id, fields)
			VALUES';
		$logs = array();
		foreach (array_keys($this->_contentTypes) AS $contentType)
		{
			$sql .= "
				('$contentType', '" . $this->_addonData['addon_id'] . "', ''),";
			$logs[] = 'content_type = ' . $this->_db->quote($contentType);
		}

		$this->_db->query(substr($sql, 0, -1));

		$sql = '
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES';
		$logs = array();
		foreach ($contentTypes AS $contentType => $fields)
		{
			foreach ($fields AS $name => $value)
			{
				$sql .= "
				('$contentType', '$name', '$value'),";
			}
		}

		$this->_db->query(substr($sql, 0, -1));

		$this->_rebuildContentTypesCache();
	}

	protected function _removeContentTypes(array $contentTypes)
	{
		foreach ($contentTypes AS $contentType => $fields)
		{
			$this->_db->delete('xf_content_type', '
				content_type = ' . $this->_db->quote($contentType) . ' AND
				addon_id = ' . $this->_db->quote($this->_existingAddon['addon_id']) . '
			');

			foreach ($fields AS $name => $value)
			{
				$this->_db->delete('xf_content_type_field', '
					content_type = ' . $this->_db->quote($contentType) . ' AND
					field_name = ' . $this->_db->quote($name) . ' AND
					field_value = ' . $this->_db->quote($value) . '
				');
			}
		}

		$this->_rebuildContentTypesCache();
	}

	protected function _rebuildContentTypesCache()
	{
		\XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
	}
}
