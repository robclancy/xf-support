<?php namespace Robbo\Support;

interface DataModelInterface {

	public function getResourceById($id, array $fetchOptions = array());

	public function getResources(array $conditions = array(), array $fetchOptions = array());

	public function getAllResources(array $fetchOptions = array());

	public function prepareResourceFetchOptions(array $fetchOptions);

	public function prepareResourceConditions(array $conditions, array &$fetchOptions);

	public function prepareResourceOrderOptions(array &$fetchOptions, $defaultOrderSql = '');

	public function insert(array $data);

	public function update($id, array $data);

	public function delete($id);
}