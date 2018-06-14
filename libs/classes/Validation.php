<?php

class Validation {
    
    private $_passed = false;
    private $_errors = array();
    private $_db = null;
    
    public function __construct() {
        $this->_db = Database::getInstance();
    }
    
    public function check($source, $items = array()) {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                
                $value = trim($source[$item]);
                $item = htmlentities($item, ENT_QUOTES, 'UTF-8');
                
                if ($rule === 'required' && empty($value)) {
                    $this->addError($item, ucfirst($item) . " is required !");
                } else if (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError($item, ucfirst($item) . " must be a minimum of {$rule_value} characters !");
                            }
                        break;
                    
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError($item, ucfirst($item) . " must be a maximum of {$rule_value} characters !");
                            }
                        break;
                    
                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $this->addError($item, ucfirst($item) . " must match {$rule_value} !"); 
                            }
                        break;
                    
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if ($check->count()) {
                                $this->addError($item,  ucfirst($item) . " already exists !");
                            }
                        break;
                        
                        case 'regex':
                            $regex = $this->{$rule_value}($value);
                            if ($regex) {
                                $this->addError($item,  ucfirst($item) . " is invalid !");
                            }
                        break;
                    }
                }
                
            }
        }
        
        if (empty($this->_errors)) {
            $this->_passed = true;
        }
        
        return $this;
    }
    
    private function addError($errorName, $errorValue) {
        $this->_errors[$errorName] = $errorValue;
    }
    
    public function errors($fieldName = false) {
        if ($fieldName) {
            if (isset($this->_errors[$fieldName])) {
                return $this->_errors[$fieldName];
            } else {
                return false;
            }
        } else {
            return $this->_errors;
        }
    }
    
    public function passed() {
        return $this->_passed;
    }
    
    private function check_regex($regex, $str) {
        if (preg_match($regex . 'i', $str) == false) {
            return true;
        }
    }
    
    public function checkPrice($str) {
        $pattern = '/^[0-9]+(\.[0-9]{1,2})?$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkNumber($str) {
        $pattern = '/^[0-9]*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkForQuotes($str) {
        $pattern = '/[\'"]/';
        return $this->check_regex($pattern, urldecode($str));
    }

    public function checkUsername($str) {
        $pattern = '/^[a-zA-Z._0-9]*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkPassword($str) {
        $pattern = '/^.*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkEmail($str) {
        $pattern = '/^[-._a-z0-9]+@[-.a-z0-9]+\.[a-z]{2,4}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkCompany($str) {
        $pattern = '/^.{2,75}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkRegNumber($str) {
        $pattern = '/^J.{10,18}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkFiscalCode($str) {
        $pattern = '/^(RO){0,1}\s*\d{4,12}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkBank($str) {
        $pattern = '/^[-.a-z0-9&]{5,20}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkBankBranch($str) {
        $pattern = '/^[-.a-z0-9&,]{5,20}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkIban($str) {
        $pattern = '/^[a-z0-9]{24}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkName($str) {
        $pattern = '/^[a-zA-Z\-\s\']*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkFirstName($str) {
        $pattern = '/^[a-zA-Z\-\s\']*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkLastName($str) {
        $pattern = '/^[a-zA-Z\-\s\']*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkPhone($str) {
        $pattern = '/^[0-9\s\-\+\.]{7,20}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkCnp($str) {
        $pattern = '/^[0-9]{13}$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkAddress($str) {
        $pattern = '/^[0-9a-zA-Z\/\.\-\'\,\s]*$/';
        return $this->check_regex($pattern, $str);
    }

    public function checkCity($str) {
        $pattern = '/^[0-9a-zA-Z\s\-]*$/';
        return $this->check_regex($pattern, $str);
    }
    
}