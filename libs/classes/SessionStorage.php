<?php

class SessionStorage {
    
    private $session_name;
    
    public function __construct($session_name = 'default') {
        $this->session_name = $session_name;
        
        if (!isset($_SESSION[$this->session_name])) {
            $_SESSION[$this->session_name] = [];
        }
    }
    
    public function exists($index) {
        return isset($_SESSION[$this->session_name][$index]);
    }
    
    public function set($index, $value) {
        $_SESSION[$this->session_name][$index] = $value;
    }
    
    public function get($index) {
        if (!$this->exists($index)) {
            return null;
        } else {
            return $_SESSION[$this->session_name][$index];
        }
    }
    
    public function all() {
        return $_SESSION[$this->session_name];
    }
    
    public function delete($index) {
        if ($this->exists($index)) {
            unset($_SESSION[$this->session_name][$index]);
        }
    }
    
    public function deleteAll() {
        unset($_SESSION[$this->session_name]);
    }
    
    public function countAll() {
        return count($this->all());
    }
    
}