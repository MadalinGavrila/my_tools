<?php
// Config DIRECTORY SEPARATOR
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Config URL / LIBS / SITE ROOT
defined('URL') ? null : define('URL', 'http://localhost/my_tools');
defined('SITE_ROOT') ? null : define('SITE_ROOT', '');
defined('LIBS') ? null : define('LIBS', SITE_ROOT.DS.'libs');

// Load basic functions
require_once LIBS . DS . 'functions' . DS . 'f-redirect.php';
require_once LIBS . DS . 'functions' . DS . 'f-output_message.php';
require_once LIBS . DS . 'functions' . DS . 'f-datetime_text.php';
require_once LIBS . DS . 'functions' . DS . 'f-strip_zeros_from_date.php';
require_once LIBS . DS . 'functions' . DS . 'f-log_action.php';
require_once LIBS . DS . 'functions' . DS . 'f-escape.php';

function classAutoLoader($class) { 
    $class = ucfirst($class);
    $path = LIBS . DS . 'classes' . DS . $class . '.php';
    
    if(is_file($path) && !class_exists($class)) {
        require_once($path);
    } else {
        die("This file name {$class}.php was not found");
    }   
}

spl_autoload_register('classAutoLoader');

$GLOBALS['config'] = array(
    'database' => array(
        'type' => 'DB_TYPE',
        'host' => 'DB_HOST',
        'username' => 'DB_USERNAME',
        'password' => 'DB_PASSWORD',
        'name' => 'DB_NAME'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 60 * 60 * 24 // 1 zi
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token',
        'login_time' => 60 * 60 * 2, // 2 ore
        'session_path' => SITE_ROOT.DS.'sessions' // salvam sesiunile in afara folderului site
    ),
    'cart' => array(
        'session_name' => 'cart'
    )
);

Session::start();

Cookie::remember();