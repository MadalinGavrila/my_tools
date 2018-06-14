<?php

class Session {
    
    public static function start($regenerate_id = 0) {
        ini_set('session.use_only_cookies', 1); // folosim doar cookies 
        ini_set('session.gc_maxlifetime', Config::get('session/login_time') + 600); // ceva mai mare decat expirarea cookie-ului
        ini_set('session.save_path', Config::get('session/session_path')); // salvam sesiunile in afara folderului site

        session_name('session'); // sesiunea, deci si cookie-ul de sesiune il denumim: session
        session_set_cookie_params(Config::get('session/login_time'));

        session_start();

        if ($regenerate_id) {
            session_regenerate_id(); // daca se apeleaza start(1) , regeneram (schimbam) id-ul sesiunii
        }

        setcookie(session_name(), session_id(), time() + Config::get('session/login_time'));  // prelungeste viata cookie-ului de la ultimul click
    }
    
    public static function destroy() {
        setcookie(session_name(), FALSE, 1, "/"); // sterge cookie-ul de sesiune
        $_SESSION = array(); // se sterg datele din $_SESSION
        session_destroy(); // se sterg datele din fisierul de sesiune
    }
    
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }
    
    public static function set($name, $value) {
        return $_SESSION[$name] = $value;
    }
    
    public static function get($name) {
        if (self::exists($name)) {
            return $_SESSION[$name];
        }
    }
    
    public static function delete($name) {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
    
    public static function message($name, $string = '') {
        if (self::exists($name)) {
            $message = self::get($name);
            self::delete($name);
            return $message;
        } else {
            if (!empty($string)) {
                self::set($name, $string); 
            }
        }
        
        return '';
    }
    
}