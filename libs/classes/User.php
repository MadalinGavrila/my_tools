<?php
use PHPMailer\PHPMailer\PHPMailer;

class User {
    
    private $_db;
    private $_data;
    private $_sessionName;
    private $_cookieName;
    private $_isLoggedIn;

    public function __construct($user = null) {
        $this->_db = Database::getInstance();
        
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');
        
        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                
                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    $this->logout();
                } 
            }
        } else {
            $this->find($user);
        }
    }
    
    public function select_all($table = 'users') {
        $data = $this->_db->query("SELECT * FROM {$table}");
        
        if ($data->count()) {
            return $data->results();
        } else {
            return [];
        }
    }
    
    public function create($fields = array()) {
        if (!$this->_db->insert('users', $fields)) {
            throw new Exception('There was a problem creating an account !');
        }
    }
    
    public function update($fields = array(), $id = null) {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        
        if (!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating !');
        }
    }
    
    public function find($user = null) {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user));
            
            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        
        return false;
    }
    
    public function login($username = null, $password = null, $remember = false) {
        if (!$username && !$password && $this->exists()) {
            Session::set($this->_sessionName, $this->data()->id);
            return true;
        } else {
            $user = $this->find($username);
            
            if ($user) {
                if ($this->data()->password === Hash::create($password, $this->data()->salt)) {
                    Session::set($this->_sessionName, $this->data()->id);
                
                    if ($remember) {
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
                    
                        if (!$hashCheck->count()) {
                            $hash = Hash::unique();
                            $this->_db->insert('users_session', array(
                                    'user_id' => $this->data()->id,
                                    'hash' => $hash
                            ));
                        } else {
                            $hash = $hashCheck->first()->hash; 
                        }
                    
                        Cookie::set($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }
                
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function logout() {
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
        
        Session::delete($this->_sessionName);
        Cookie::detele($this->_cookieName);
        
        Session::destroy();
    }
    
    public function hasPermission($key) {
        $group = $this->_db->get('users_group', array('id', '=', $this->data()->users_group));
       
        if ($group->count()) {
            $permissions = json_decode($group->first()->permissions, true);
            
            if ($permissions[$key] == true) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getRole($users_group) {
        $group = $this->_db->get('users_group', ['id', '=', $users_group]);
        
        if($group->count()) {
            return $group->first()->name;
        }
    }
    
    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }
    
    public function data() {
        return $this->_data;
    }
    
    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }
    
    public function findByEmail($email = null) {
        if($email) {
            $data = $this->_db->get('users', array('email', '=', $email));
            
            if($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        
        return false;
    }
    
    public function recover($mode, $email) {
        if($this->findByEmail($email)) {
            if($mode == 'username') {
                return $this->sendMail($email, 'Recover username', 'For ' . $this->data()->email . ' username is: ' . $this->data()->username);
            } else if($mode == 'password') {                
                try {
                    $generated_password = substr(md5(rand(999, 999999)), 0, 8);
                    $salt = Hash::salt(32);
                    
                    $this->update([
                        'password' => Hash::create($generated_password, $salt),
                        'salt' => $salt,
                        'password_recover' => 1
                    ], $this->data()->id);
                    
                    return $this->sendMail($email, 'Recover password', 'For ' . $this->data()->email . ' password is: ' . $generated_password);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }     
            }
            
            return false;
        }
        
        return false;
    }
    
    public function activate($email, $email_code) {
        if($this->findByEmail($email)) {
            if($this->data()->email_code == $email_code && $this->data()->active == 0) {
                try {
                    $this->update(['active' => 1], $this->data()->id);
                    
                    return true;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        
        return false;
    }
    
    public function sendMail($email, $subject, $body) {             
        $mail = new PHPMailer();
                
        $mail->isSMTP();                                      
        $mail->Host = getenv('SMTP_HOST');                      
        $mail->Username = getenv('SMTP_USER');                 
        $mail->Password = getenv('SMTP_PASSWORD');
        $mail->Port = getenv('SMTP_PORT');
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
                                     
        $mail->setFrom('madalin.gavrila13@yahoo.com', 'Madalin Gavrila');
        $mail->addAddress($email);
        
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        return $mail->send();
    }
    
}