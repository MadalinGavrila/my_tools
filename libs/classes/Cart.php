<?php

class Cart {
    
    private $storage;
    private $product;
    
    public function __construct() {
        $this->storage = new SessionStorage(Config::get('cart/session_name'));
        $this->product = new Product();
    }
    
    public function exists($product) {
        return $this->storage->exists($product->id);
    }
    
    public function get($product) {
        return $this->storage->get($product->id);
    }
    
    public function update($product, $quantity) {
        if (!$this->product->find($product->id)->hasStock($quantity)) {
            throw new Exception('You have added the maximum stock for this item !');
        }
        
        if ($quantity == 0) {
            $this->remove($product);
            return;
        }
        
        $this->storage->set($product->id, [
            'product_id' => (int) $product->id,
            'quantity' => (int) $quantity
        ]);
    }
    
    public function add($product, $quantity) {
        if ($this->exists($product)) {
            $quantity = $this->get($product)['quantity'] + $quantity;
        }
        
        $this->update($product, $quantity);
    }
    
    public function remove($product) {
        $this->storage->delete($product->id);
    }
    
    public function removeAll() {
        $this->storage->deleteAll();
    }
    
    public function all() {
        $items = [];
        $products = [];
        
        foreach ($this->storage->all() as $product) {
            $products[] = $this->product->find($product['product_id'])->data();
        }
        
        foreach ($products as $product) {
            $product->quantity = $this->get($product)['quantity'];
            $items[] = $product;
        }
        
        return $items;
    }
    
    public function itemCount() {
        return $this->storage->countAll();
    }
    
    public function subTotal() {
        $total = 0;
        
        foreach ($this->all() as $item) {
            if ($this->product->find($item->id)->outOfStock()) {
                continue;
            }
            
            $total = $total + $item->price * $item->quantity;
        }
        
        return $total;
    }
    
    public function refresh() {
        foreach ($this->all() as $item) {
            if (!$this->product->find($item->id)->hasStock($item->quantity)) {
                $this->update($item, $item->stock);
            }
        }
    }
    
    public function removeQuantity($product, $quantity) {
        if ($this->exists($product)) {
            $quantity = $this->get($product)['quantity'] - $quantity;
        }
        
        $this->update($product, $quantity);
    }
    
}