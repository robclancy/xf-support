<?php namespace Robbo\Support;

interface DataModelInterface {

	public function getResourceById($id, array $fetchOptions = array());

	public function getAllResources(array $fetchOptions = array());

	public function getResourceJoinOptions(array $fetchOptions);
}