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
}