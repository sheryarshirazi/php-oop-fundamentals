<?php

class Validator
{
    /**
     * Validation errors
     * @var array
     */
    private $errors = array();

    /**
     * Validate data against a set of rules and set errors in the $this->errors
     * array
     * @param array $data
     * @param array $rules
     * @return boolean
     */
    public function validate (Array $data, Array $rules)
    {
        $valid = true;
        /**
         * item can be email, password , username etc.
         * @var associative array
         *
         * $ruleset build like required|email|min:8
         * @var string
         */
        foreach ($rules as $item => $ruleset) {
            // required|email|min:8
            $ruleset = explode('|', $ruleset);

            foreach ($ruleset as $rule) {

                // if rule contain : then $pos variable is set
                $pos = strpos($rule, ':');

                // if rule has parameter i.e 8
                if ($pos !== false) {
                    // parameter is after the colon ($pos now string position of :)
                    $parameter = substr($rule, $pos + 1);
                    // vise versa rule is before the :
                    $rule = substr($rule, 0, $pos);
                }
                else {
                    $parameter = '';
                }

                // validateEmail($item, $value, $param)
                $methodName = 'validate' . ucfirst($rule);
                $value = isset($data[$item]) ? $data[$item] : NULL;
                if (method_exists($this, $methodName)) {
                    $this->$methodName($item, $value, $parameter) OR $valid = false;
                }
            }
        }


        return $valid;
    }

    /**
     * Get validation errors
     * @return array:
     */
    public function getErrors ()
    {
        return $this->errors;
    }

    /**
     * Validate the $value of $item to see if it is present and not empty
     * @param string $item
     * @param string $value
     * @param string $parameter
     * @return boolean
     */
    private function validateRequired ($item, $value, $parameter)
    {
        if ($value === '' || $value === NULL) {
            $this->errors[$item][] = 'The ' . $item . ' field is required';
            return false;
        }

        return true;
    }

    /**
     * Validate the $value of $item to see if it is a valid email address
     * @param string $item
     * @param string $value
     * @param string $parameter
     * @return boolean
     */
    private function validateEmail ($item, $value, $parameter)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$item][] = 'The ' . $item . ' field should be a valid email addres';
            return false;
        }

        return true;
    }

    /**
     * Validate the $value of $item to see if it is fo at least $param
     * characters long
     * @param string $item
     * @param string $value
     * @param string $parameter
     * @return boolean
     */
    private function validateMin ($item, $value, $parameter)
    {
        if (strlen($value) >= $parameter == false) {
            $this->errors[$item][] = 'The ' . $item . ' field should have a minimum length of ' . $parameter;
            return false;
        }

        return true;
    }
}
