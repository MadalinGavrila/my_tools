<?php

class Upload {
    
    public $file_name;
    
    private $upload_dir; // SITE_ROOT 
    private $file_size; // 10485760 = 10 MB
    private $file_types; // array('image/gif', 'image/jpeg', 'image/png')
    private $file_errors = array();
    
    private $temp_path;
    private $type;
    private $size;
    
    // http://www.php.net/manual/en/features.file-upload.errors.php
    private $upload_errors = array(
        UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize !",
        UPLOAD_ERR_FORM_SIZE => "The file size is too large !",
        UPLOAD_ERR_PARTIAL => "Partial upload !",
        UPLOAD_ERR_NO_FILE => "No file was uploaded !",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory !",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk !",
        UPLOAD_ERR_EXTENSION => "File upload stopped by extension !"
    );
    
    public function set_upload_dir($upload_dir) {
        $this->upload_dir = $upload_dir;
    }
    
    public function set_file_types($file_types) {
        $this->file_types = $file_types;
    }
    
    public function set_file_size($file_size) {
        $this->file_size = $file_size;
    }
    
    public function get_file_size() {
        return $this->file_size;
    }
    
    // $_FILES['uploaded_file'] as an argument
    public function attach_file($file) {
        if ($file['error']) {
            $this->file_errors[] = $this->upload_errors[$file['error']];
            return false;
        } else {
            $this->temp_path = $file['tmp_name'];
            $this->file_name = basename($file['name']);
            $this->type = $file['type'];
            $this->size = $file['size'];
            return true;
        }
    }
    
    private function file_path() {
        return $this->upload_dir . DS . $this->file_name;
    }
    
    public function create() {
        // Make sure there are no errors
        if (!empty($this->file_errors)) {
            return false;
        }
        
        // Wrong extension
        if (!in_array($this->type, $this->file_types)) {
            $this->file_errors[] = "File extension is not allowed !";
            return false;
        }

        // Wrong size
        if ($this->size > $this->file_size) {
            $this->file_errors[] = "The file size is too large !";
            return false;
        }

        // Can't save without filename and temp location
        if (empty($this->file_name) || empty($this->temp_path)) {
            $this->file_errors[] = "The file location was not available !";
            return false;
        }

        // Make sure a file doesn't already exist in the target location
        if (file_exists($this->file_path())) {
            $this->file_errors[] = "The file {$this->file_name} already exists !";
            return false;
        }

        // Attempt to move the file
        if (move_uploaded_file($this->temp_path, $this->file_path())) {
            unset($this->temp_path);
            return true;
        } else {
            $this->file_errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder !";
            return false;
        }
    }
    
    public function delete() {
        if (file_exists($this->file_path())) {
            return unlink($this->file_path()) ? true : false;
        } else {
            return false;  
        }
    }
    
    public function error() {
        return $this->file_errors;
    }
    
}