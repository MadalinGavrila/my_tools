<?php

class Cookie {
    
    public static function remember() {
        if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
            $hash = Cookie::get(Config::get('remember/cookie_name'));
            $hashCheck = Database::getInstance()->get('users_session', array('hash', '=', $hash));
    
            if ($hashCheck->count()) {
                $user = new User($hashCheck->first()->user_id);
                $user->login();
            }
        }
    }
    
    public static function exists($name) {
        return (isset($_COOKIE[$name])) ? true : false;
    }
    
    public static function get($name) {
        if (self::exists($name)) {
            return $_COOKIE[$name];
        } 
    }
    
    public static function set($name, $value, $expiry) {
        if (setcookie($name, $value, time() + $expiry, '/')) {
            return true;
        }
        
        return false;
    }
    
    public static function detele($name) {
        self::set($name, '', time() - 1);
    }
    
}