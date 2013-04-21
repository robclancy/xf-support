<?php namespace Robbo\Support;

class Repository implements RepositoryInterface {

	protected $model;

	public function __construct(DataModelInterface $model)
	{
		$this->model = $model;
	}

	public function getById($id, array $fetchOptions = array())
	{
		return $this->model->getResourceById($id, $fetchOptions);
	}

	public function getAll(array $fetchOptions = array())
	{
		return $this->model->getAllResources($fetchOptions);
	}

	public function get(array $conditions = array(), array $fetchOptions = array())
	{
		return $this->model->getResources($conditions, $fetchOptions);
	}

	public function save($id, array $data, array $extra = array())
	{
		return $id ? $this->update($id, $data, $extra) : $this->insert($data, $extra);
	}

	public function insert(array $data, array $extra = array())
	{
		return $this->model->insert($data, $extra);
	}

	public function update($id, array $data, array $extra = array())
	{
		return $this->model->update($id, $data, $extra);
	}

	public function delete($id)
	{
		return $this->model->delete($id);
	}
}
