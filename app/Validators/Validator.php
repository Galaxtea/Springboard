<?php

namespace App\Validators;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Validator as LaravelValidator;

abstract class Validator
{
    /*
    |--------------------------------------------------------------------------
    | Base Validator class
    |--------------------------------------------------------------------------
    |
    | Validators handle basic sanitisation & checking to see if a user input is valid. 
    |
    */

    /**
     * Array of validation rules.
     * @var array
     */
    protected $rules = [];

    /**
     * Array of custom messages.
     * @var array
     */
    protected $messages = [];

    /**
     * Array of custom validator functions.
     * @var array
     */
    protected $validators = [];

    /**
     * Array of sanitisation functions.
     * @var array
     */
    protected $sanitizers = [];

    /**
     * Array of data.
     * @var array
     */
    public $data = [];

    /**
     * MessageBag of errors.
     * @var Illuminate/Support/MessageBag
     */
    protected $errors = null;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    /**
     * Validate the data against the defined validation rules.
     * @return boolean
     */
    public function validate($data, $ruleset = 'create')
    {
        // We allow collections, so transform to array.
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        // Execute sanitizers over the data before validation.
        $data = $this->sanitize($data);

        // Load the correct ruleset.
        $rules = $this->rules[$ruleset];

        // Load the correct messageset.
        $messages = isset($this->messages) ? $this->messages : [];

        // Create the validator instance and validate.
        if($rules && is_array($rules) && count($rules) > 0) {
            $validator = LaravelValidator::make($data, $rules, $messages);
            if (!$result = $validator->passes()) {
                $this->errors = $validator->errors();
            }
        } else {
            $result = true;
        }

        $this->data = $data;
        
        // Execute custom validation methods.
        if(!$this->runValidators($data, $ruleset)) {
            $result = false;
        }

        $this->data = $data;

        // Return the validation result.
        return $result;
    }

    public function beforeValidate()
    {
        return true;
    }

    /**
     * Return the sanitized data.
     * @return array
     */
    public function data($key = null)
    {
        if(is_null($key)) return $this->data;
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Sanitise the data.
     * @return array
     */
    public function sanitize($data) 
    {
        return $this->runSanitizers($data);
    }

    /**
     * Sanitise the data using the listed sanitizers.
     * @return array
     */
    public function runSanitizers($data) 
    {
        // Iterate through all of the sanitizer methods.
        foreach ($this->sanitizers as $sanitizer) {
            $method = 'sanitize' . Str::studly($sanitizer);
            if (isset($data[$sanitizer]) && method_exists($this, $method)) {
                $this->data = $data = call_user_func([$this, $method], $data);
            }
        }
        return $data;
    }

    /**
     * Run custom validators.
     * @return array
     */
    public function runValidators($data, $ruleset) 
    {
        $result = true; 
        if(!isset($this->validators[$ruleset])) return $result;
        foreach ($this->validators[$ruleset] as $validator) {
            $method = 'validate' . Str::studly($validator);
            //if (isset($data[$validator]) && method_exists($this, $method)) {
            if (method_exists($this, $method)) {
                if(!call_user_func([$this, $method], $data)) $result = false;
            }
        }
        return $result;
    }

    /**
     * Return errors.
     * @return Illuminate/Support/MessageBag
     */
    public function errors() 
    {
        return $this->errors;
    }

    /**
     * Return all error messages.
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errors->unique();
    }

}