XenForo Support Package
==========

This package is a bunch of things to remove a lot of boilerplate when working with XenForo.

## Installation
Coming soon...

## Examples

Note: a lot of this is still fairly boilerplate and tedious, I will have xf-toolkit generate a lot of code in the future when I get to it.

### Data Model

This is designed to have methods which only work with the database. Other model data, for example resizing a thumbnail should be in a traditional model as these models are designed to work with repositories which are detailed below.

```php
class MyModel extends \Robbo\Support\DataModel {

	$this->_table = 'my_table';

	$this->_key = 'table_id';
}
```

And that is it to implement everything you see in `Robbo\Support\DataModelInterface`. 

You still have to define your own joins, conditions and orders.

```php
class MyModel extends \Robbo\Support\DataModel {

	$this->_table = 'my_table';

	$this->_key = 'table_id';

	const FETCH_MY_OTHER_TABLE = 0x01;

	public function getResourceJoinOptions(array $fetchOptions)
	{	
		$selectFields = '';
		$joinTables = '';

		if ( ! empty($fetchOptions['join']))
		{
			if ($fetchOptions['join'] & self::FETCH_MY_OTHER_TABLE)
			{
				$selectFields .= ',
					other_table.*';

				$joinTables .= '
					INNER JOIN other_table ON (my_table.table_id = other_table.table_id)';
			}
		}

		return array('selectFields' => $selectFields, 'joinTables' => $joinTables);
	}

	public function prepareResourceConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();

		if (isset($conditions['something']))
		{
			$sqlConditions[] = 'my_table.something = ' . $db->quote($conditions['something']);
		}

		return $this->getConditionsForClause($sqlConditions);
	}

	protected function prepareResourceOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
	{
		return 'TODO: add order example here';
	}
}
```

### Repositories

These are essentially wrappers for data models for the purpose of allowing for easier unit testing and cleaner code.
There is a concrete implementation `Robbo\Support\Repository` however you can extend or implement your own as the interface is passed around and not the concrete class.

To use the concrete implementation you need to give it a data model to use. Here is an example in a traditional controller using our above data model.

```php

class MyController extends XenForo_ControllerPublic_Abstract {
	
	public function actionIndex()
	{
		$repository = new \Robbo\Support\Repository($this->getModelFromCache('MyModel'));

		return $this->responseView('mytemplate', array(
			'myData' => $repository->getAll()
		));
	}

	public function actionEdit()
	{
		$repository = new \Robbo\Support\Repository($this->getModelFromCache('MyModel'));
		$id = $this->_input->filterSingle('id', XenForo_Input::UINT);

		if ( ! $resource = $repository->getById($id))
		{
			return $this->responseNoPermission();
		}

		return $this->responseView('mytemplate_edit', array(
			'myData' => $resource
		));
	}

	public function actionSave()
	{
		$repository = new \Robbo\Support\Repository($this->getModelFromCache('MyModel'));
		$input = $this->_input->filter(array('one' => XenForo_Input::UINT, 'two' => XenForo_Input::STRING));
		$id = $this->_input->filterSingle('id', XenForo_Input::UINT);

		if ( ! $repository->getById($id))
		{
			return $this->responseNoPermission();
		}

		$repository->save($id, $input);

		return $this->responseRedirect('somewhere');
	}
}
```

Now there is still a fair bit of boilerplate going on there, which leads me to the next example...

### Controllers

Controllers really just have little helpers. For one instead of having to type out `XenForo_Input::UINT` you can do `self::UINT`.

Then there are helpers for creating the repositories and models for you early in the controllers lifespan. So the above controller can be simplified to the following:

class MyController extends XenForo_ControllerPublic_Abstract {
	
	protected $_dataModelName = 'MyModel';

	protected $_idName = 'id';

	public function actionIndex()
	{
		return $this->responseView('mytemplate', array(
			'myData' => $this->repository->getAll()
		));
	}

	public function actionEdit()
	{
		if ( ! $resource = $this->repository->getById($this->_id)
		{
			return $this->responseNoPermission();
		}

		return $this->responseView('mytemplate_edit', array(
			'myData' => $resource
		));
	}

	public function actionSave()
	{
		if ( ! $this->repository->getById($this->_id))
		{
			return $this->responseNoPermission();
		}

		$this->repository->save($this->_id,  $this->_input->filter(array('one' => self::UINT, 'two' => self::STRING)));

		return $this->responseRedirect('somewhere');
	}
}
```

### DataWriters

I hate having to write out all that boilerplate for datawriters just like I had to for models. So I added a few little shortcuts to make it less tedious and even easier to read.

```php

class MyWriter extends \Robbo\Support\DataWriter {
	
	/* Old way
	protected function _getFields()
	{
		return array(
			'merc_gallery_media' => array(
				'media_id' 		=> array('type' => self::TYPE_UINT, 	'autoIncrement' => true),
				'category_id' 	=> array('type' => self::TYPE_UINT, 	'required' => true),
				'user_id' 		=> array('type' => self::TYPE_UINT, 	'required' => true),
				'username' 		=> array('type' => self::TYPE_STRING, 	'required' => true, 'maxLength' => 50),
				'ip_id'			=> array('type' => self::TYPE_UINT,   	'default' => 0),
				'image' 		=> array('type' => self::TYPE_STRING, 	'default' => '', 	'maxLength' => 50),
				'video' 		=> array('type' => self::TYPE_STRING, 	'default' => '', 	'maxLength' => 255),
				'title' 		=> array('type' => self::TYPE_STRING, 	'required' => true, 'maxLength' => 100),
				'description' 	=> array('type' => self::TYPE_STRING, 	'default' => ''),
				'media_state'  => array('type' => self::TYPE_STRING, 	'default' => 'visible',
					'allowedValues' => array('visible', 'moderated', 'deleted')
				),
				'added_date' 	=> array('type' => self::TYPE_UINT, 	'default' => XenForo_Application::$time),
				'upload_date' 	=> array('type' => self::TYPE_UINT, 	'default' => XenForo_Application::$time),
				'view_count' 	=> array('type' => self::TYPE_UINT, 	'default' => 0),
				'likes' 		=> array('type' => self::TYPE_UINT_FORCED, 'default' => 0),
				'like_users' 	=> array('type' => self::TYPE_SERIALIZED, 'default' => 'a:0:{}'),
			)
		);
	}

	protected function _getExistingData($data)
	{
		if ( ! $id = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		return array('merc_gallery_media' => $this->getModelFromCache('Merc_Gallery_Model_Media')->getMediaById($id));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'media_id = ' . $this->_db->quote($this->getExisting('media_id'));
	}*/

	// New way starting here...

	protected $_table = 'merc_gallery_media';

	protected $_key = 'media_id';

	protected function _setFields()
	{
		$this->_field('media_id')->uinteger()->auto();
		$this->_filed('category_id')->uinteger()->required();
		$this->_field('user_id')->uinteger()->required();
		$this->_field('username')->string(50)->required();
		$this->_field('ip_id')->uinteger()->default(0);
		$this->_field('image')->string(50);
		$this->_field('video')->string(255);

		// And so on... this is still fairly tedious so I will be looking at ways to improve it
	}

	protected function _getExistingData($data)
	{
		return $this->_genericExistingData(
			'merc_gallery_media', 
			'media_id', 
			$this->_createRepository($this->getModelFromCache('MyModel')), 
			$data
		);
	}

	protected function _getUpdateCondition($tableName)
	{
		return $this->_genericUpdateCondition($tableName, 'media_id');
	}
}
```

### Route Prefixes

I find that most my route prefixes are the same thing over and over. So I made it so I could just define a couple variables.

An old prefix...
```php
class Merc_Sidebar_Route_PrefixAdmin_Blocks implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'block_id');
		return $router->getRouteMatch('Merc_Sidebar_ControllerAdmin_Block', $action, 'sidebars');
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'block_id', 'title');
	}
}
```

Now the same thing extending the support class instead...
```php
class Merc_Sidebar_Route_PrefixAdmin_Blocks extends \Robbo\Support\RoutePrefix {
	
	protected $_controller = 'Merc_Sidebar_ControllerAdmin_Block';

	protected $_id = 'block_id';

	protected $_group = 'sidebars';
}
```