<?php

namespace App\Services;

use App;
use Illuminate\Support\MessageBag;


use App\Helpers\AuthShare;
abstract class Service extends AuthShare {
	/*
	|--------------------------------------------------------------------------
	| Base Service
	|--------------------------------------------------------------------------
	|
	| Base service, setting up error handling.
	|
	*/
	public function __construct() {
		parent::__construct();
		$this->resetErrors();
	}


	/**
	 * Errors.
	 * @var Illuminate\Support\MessageBag
	 */
	protected $errors = null;
	protected $cache = [];


	/**
	 * Calls a service method and injects the required dependencies.
	 * @param string $methodName
	 * @return mixed
	 */
	protected function callMethod($methodName)
	{
		if(method_exists($this, $methodName)) return App::call([$this, $methodName]);
	}

	/**
	 * Return if an error exists. 
	 * @return bool
	 */
	public function hasErrors()
	{
		return $this->errors->count() > 0;
	}

	/**
	 * Return if an error exists. 
	 * @return bool
	 */
	public function hasError($key)
	{
		return $this->errors->has($key);
	}

	/**
	 * Return errors. 
	 * @return Illuminate\Support\MessageBag
	 */
	public function errors()
	{
		return $this->errors;
	}
	/**
	 * Return errors. 
	 * @return array
	 */
	public function getAllErrors()
	{
		return $this->errors->unique();
	}

	/**
	 * Return error by key. 
	 * @return Illuminate\Support\MessageBag
	 */
	public function getError($key)
	{
		return $this->errors->get($key);
	}

	/**
	 * Empty the errors MessageBag.
	 * @return void
	 */
	public function resetErrors()
	{
		$this->errors = new MessageBag();
	}

	/**
	 * Add an error to the MessageBag.
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	protected function setError($key, $value)
	{
		$this->errors->add($key, $value);
	}

	/**
	 * Add multiple errors to the message bag
	 * @param Illuminate\Support\MessageBag $errors
	 * @return void
	 */
	protected function setErrors($errors) 
	{
		$this->errors->merge($errors);
	}

	/**
	 * Commits the current DB transaction and returns a value.
	 * @param mixed $return
	 * @return mixed $return
	 */
	protected function commitReturn($return = true)
	{
		\DB::commit();
		return $return;
	}

	/**
	 * Rolls back the current DB transaction and returns a value.
	 * @param mixed $return
	 * @return mixed $return
	 */
	protected function rollbackReturn($return = false)
	{
		\DB::rollback();
		return $return;
	}

	/**
	 * Returns the current field if it is numeric, otherwise searches for a field if it is an array or object.
	 * @param mixed $data
	 * @param string $field
	 * @return mixed 
	 */
	protected function getNumeric($data, $field = 'id')
	{
		if(is_numeric($data)) return $data;
		elseif(is_object($data)) return $data->$field;
		elseif(is_array($data)) return $data[$field];
		else return 0;
	}












// Below is outdated caching
	public function remember($key = null, $fn = null)
	{
		if(isset($this->cache[$key])) return $this->cache[$key];
		return $this->cache[$key] = $fn();
	}
	public function forget($key)
	{
		unset($this->cache[$key]);
	}

	/**
	 * Returns the cache key using query string.
	 * @param string $string
	 * @return string
	 */
	public function cacheKey($string)
	{
		$query_params = request()->query();
		ksort($query_params);
		$query_string = http_build_query($query_params);

		return "{$string}?{$query_string}";
	}
}
