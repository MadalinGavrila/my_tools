<?php

class Hash {
    
    public static function create($string, $salt = '', $algo = 'sha256') {
        $result = hash_init($algo, HASH_HMAC, $salt);
        hash_update($result, $string);

        return hash_final($result);
    }
    
    public static function create2($string, $salt = '', $algo = 'sha256') {
        return hash($algo, $string . $salt);
    }
    
    public static function salt($length) {
        return utf8_encode(mcrypt_create_iv($length));
    }
    
    public static function unique() {
        return self::create2(uniqid());
    }
    
}