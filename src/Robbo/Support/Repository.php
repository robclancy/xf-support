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

	public function getSome(array $conditions = array(), array $fetchOptions = array())
	{
		return $this->model->getResources($conditions, $fetchOptions);
	}

	public function insert(array $data)
	{
		return $this->model->insert($data);
	}

	public function update($id, array $data)
	{
		return $this->model->update($id, $data);
	}

	public function delete($id)
	{
		return $this->model->delete($id);
	}
}