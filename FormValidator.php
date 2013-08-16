<?php

/**
 * Class FormValidator
 */
class FormValidator
{
    /**
     * Strings for error messages
     * @var array
     */
    private $errorMessages = array();

    /**
     * Validation functions
     * @var array
     */
    private $validationFunctions = array();

    /**
     * Create an instance of FormValidator and add <required, min, max, minlen, maxlen, int> validations
     */
    public function __construct()
    {
        $this->addRule('required', function ($data) {
            return !empty($data);
        }, '%s is required');
        $this->addRule('min', function ($data, $min) {
            return !($data < $min);
        }, '%s can not be less than %d');
        $this->addRule('max', function ($data, $max) {
            return !($data > $max);
        }, '%s can not be larger than %d');
        $this->addRule('minlen', function ($data, $min) {
            return !(mb_strlen($data) < $min);
        }, '%s can not be less then %d characters');
        $this->addRule('maxlen', function ($data, $max) {
            return !(mb_strlen($data) > $max);
        }, '%s can not be larger then %d characters');
        $this->addRule('int', function ($data) {
            return (int)$data === $data;
        }, '%s must be an integer');
    }

    /**
     * Parse a rule
     *
     * @param string $rule Rule definition
     * @return array Rule definition and parameters
     */
    private function parseRule($rule)
    {
        return preg_split('#[\(, \)]#', $rule, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Add a validation
     *
     * @param string $name Name of validation
     * @param mixed $function Validation function
     * @param string $string Error message
     */
    public function addRule($name, $function, $string)
    {
        $this->validationFunctions[$name] = $function;
        $this->errorMessages[$name] = $string;
    }

    /**
     * Add custom error message for $name
     *
     * @param string $name Name of validation
     * @param string $string Error message (Can use string format characters, first one will key of data and the others will parameters of validation)
     */
    public function addErrorMessage($name, $string)
    {
        $this->errorMessages[$name] = $string;
    }

    /**
     * Add custom error messages
     *
     * @param array $messages Error messages ($name => $message) (Can use string format characters for message, first one will key of data and the others will parameters of validation)
     */
    public function addErrorMessages($messages)
    {
        foreach ($messages as $name => $message)
            $this->errorMessages[$name] = $message;
    }

    /**
     * Check $data is valid for $rules
     *
     * @param array $data Data which want to validate ($key => $value)
     * @param array $rules Rule definitions ($key => array($rule1, $rule2(param1,param2 ... paramN), ... $ruleN))
     * @return array|bool TRUE if $data pass, or ARRAY of ERRORS
     */
    public function isValid($data, $rules)
    {
        $errors = array();

        foreach ($rules as $key => $rules)
            foreach ($rules as $rule) {
                //First value is name of validation, and the others are parameters of that
                $params = $this->parseRule($rule);

                //Switch name of validation and data which checking
                $rule = $params[0];
                $params[0] = $data[$key];

                if (call_user_func_array($this->validationFunctions[$rule], $params) === false) {
                    //Switch data's key
                    $params[0] = $key;
                    $errors[] = vsprintf($this->errorMessages[$rule], $params);
                }
            }

        return !$errors ? true : $errors;
    }
}