<?php namespace Robbo\Support;

interface RepositoryInterface {

	public function getById($id, array $fetchOptions = array());

	public function getAll(array $fetchOptions = array());

	public function getSome(array $conditions = array(), array $fetchOptions = array());

	public function insert(array $data);

	public function update($id, array $data);

	public function delete($id);
}