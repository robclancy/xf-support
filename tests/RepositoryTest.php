<?php

use Mockery as m;

class RepositoryTest extends PHPUnit_Framework_TestCase {

	// These are just basic things so I group them in the one test
	public function testMethodCallsWork()
	{
		$model = m::mock('Robbo\Support\DataModelInterface');

		$repository = new Robbo\Support\Repository($model);

		$model->shouldReceive('getResourceById')->once()->with('id', array())->andReturn(array('test'));
		$this->assertEquals(array('test'), $repository->getById('id'));

		$model->shouldReceive('getAllResources')->once()->with(array())->andReturn(array('test2'));
		$this->assertEquals(array('test2'), $repository->getAll());

		$model->shouldReceive('getResources')->once()->with(array('test' => 2), array())->andReturn(array('sommme'));
		$this->assertEquals(array('sommme'), $repository->get(array('test' => 2)));

		$model->shouldReceive('insert')->once()->with(array('name' => 'test'))->andReturn(array('id' => 1, 'name' => 'test'));
		$this->assertEquals(array('id' => 1, 'name' => 'test'), $repository->insert(array('name' => 'test')));

		$model->shouldReceive('update')->once()->with(2, array('name' => 'test'))->andReturn(array('id' => 2, 'name' => 'test'));
		$this->assertEquals(array('id' => 2, 'name' => 'test'), $repository->update(2, array('name' => 'test')));

		$model->shouldReceive('delete')->once()->with(1)->andReturn(true);
		$this->assertEquals(true, $repository->delete(1));
	}
}