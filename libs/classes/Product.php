<?php

class Product {
    
    private $_db;
    private $_data;
    
    public function __construct($product = null) {
        $this->_db = Database::getInstance();
        
        if ($product) {
            $this->find($product);
        }
    }
    
    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }
    
    public function data() {
        return $this->_data;
    }
    
    public function select_all($table = 'products') {
        $data = $this->_db->query("SELECT * FROM {$table}");
        
        if ($data->count()) {
            return $data->results();
        } else {
            return [];
        }
    }
    
    public function create($fields = array()) {
        if (!$this->_db->insert('products', $fields)) {
            throw new Exception('There was a problem creating an product !');
        }
    }
    
    public function update($id, $fields = array()) {
        if (!$this->_db->update('products', $id, $fields)) {
            throw new Exception('There was a problem updating !');
        }
    }
    
    public function delete($where = array()) {
        if (!$this->_db->delete('products', $where)) {
            throw new Exception('There was a problem deleting !');
        } else {
            return true;
        }
    }
    
    public function find($product = null) {
        if ($product) {
            $data = $this->_db->get('products', array('id', '=', $product));
            
            if ($data->count()) {
                $this->_data = $data->first();
                return $this;
            }
        }
        
        return false;
    }
    
    public function hasLowStock() {
        if ($this->outOfStock()) {
            return false;
        } else {
            return (bool) ($this->data()->stock <= 5);
        }
    }
    
    public function outOfStock() {
        return $this->data()->stock == 0;
    }
    
    public function inStock() {
        return $this->data()->stock >= 1;
    }
    
    public function hasStock($quantity) {
        return $this->data()->stock >= $quantity;
    }
    
}