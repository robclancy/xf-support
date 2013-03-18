<?php namespace Robbo\Support;

interface RepositoryInterface {

	public function getById($id, array $fetchOptions = array());
}