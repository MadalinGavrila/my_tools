<?php

class Form {

    protected $formData;
    protected $initData;
    protected $method;

    public function __construct($method) {
        $this->method = strtolower($method);

        if ($this->method === 'get') {
            $this->setFormData($_GET);
        }
        if ($this->method === 'post') {
            $this->setFormData($_POST);
        }
    }

    protected function setFormData(array $formData) {
        $this->formData = $formData;
        if (isset($formData) && is_array($formData)) {
            foreach ($this->formData as $key => $value) {
                if (!is_array($value)) {
                    $this->formData[$key] = trim($value);
                } else {
                    $this->formData[$key] = $value;
                }
            }
        }
    }

    public function setFormDataField($formFields) {
        if (isset($this->formData) && is_array($this->formData)) {
            foreach ($formFields as $field) {
                if (!isset($this->formData[$field])) {
                    $this->formData[$field] = '';
                }
            }
        }
    }

    public function fetchData($fieldName = false) {
        if ($fieldName) {
            if (isset($this->formData[$fieldName])) {
                return $this->formData[$fieldName];
            } else {
                return false;
            }
        } else {
            return $this->formData;
        }
    }
    
    public function startForm($formName, $action = "", $enctype = "", $formParams = "") {
        if (!empty($enctype)) {
            $formParams .= ' enctype="' . $enctype . '"';
        }
        $html = '<form name="' . $formName . '" id="' . $formName . '" method="' . $this->method . '" action="' . $action . '" ' . $formParams . '>';

        return $html;
    }

    public function endForm() {
        $html = '</form>';
        return $html;
    }
    
    public function label($for, $value, $params = '') {
        $html = '<label for="' . $for . '" ' . $params . '>' . $value . '</label>';
        return $html;
    }

    public function setInitData($fieldName, $data) {
        $this->initData[$fieldName] = $data;
    }

    public function addInitData($initData) {
        if (isset($this->initData) && is_array($this->initData)) {
            $initData = array_merge($initData, $this->initData);
        }
        $this->initData = $initData;
    }

    protected function fetchFromArraySintax($strig) {
        $pieces = preg_split('/\[|\]/', $strig);
        $name = $pieces[0];
        $key = $pieces[1];
        return array('name' => $name, 'key' => $key);
    }

    public function text($name, $params = '', $type = 'text', $value = null) {
        if (isset($this->formData[$name])) {
            $elementData = htmlentities($this->formData[$name], ENT_QUOTES, 'UTF-8');
        } else if (strpos($name, '[') !== FALSE) {
            $x = $this->fetchFromArraySintax($name);
            $aName = $x["name"];
            $aKey = $x["key"];
            if (isset($this->formData[$aName][$aKey])) {
                $elementData = $this->formData[$aName][$aKey];
            }
        }

        if (isset($value)) {
            $elementData = $value;
        }

        $field = '<input type="' . $type . '" id="' . $name . '" name="' . $name . '"';

        if (isset($elementData)) {
            $field .= ' value="' . $elementData . '" ';
        }

        if ($params != '') {
            $field .= ' ' . $params;
        }

        $field .= ' />';

        return $field;
    }

    public function password($name, $params = '') {
        $field = $this->text($name, $params, 'password');
        return $field;
    }

    public function hidden($name, $value) {
        $field = $this->text($name, '', 'hidden', $value);
        return $field;
    }

    public function ck($name, $value, $params = '', $type = 'checkbox') {
        $checked = false;

        if (isset($this->formData[$name])) {
            $elementData = $this->formData[$name];
            if (is_array($elementData) && in_array($value, $elementData)) {
                $checked = true;
            } else {
                if ($elementData == $value) {
                    $checked = true;
                }
            }
        }

        if ($checked) {
            $ck_str = ' checked = "checked" ';
        } else {
            $ck_str = '';
        }

        if ($type == 'checkbox') {
            $name .= '[]';
        }

        $field = '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" ' . $params . ' ' . $ck_str . '/>';

        return $field;
    }

    public function radio($name, $value, $params = '') {
        $field = $this->ck($name, $value, $params, 'radio');
        return $field;
    }

    public function select($name, $type = 'single', $params = '', $value = null) {
        $initData = $this->initData[$name];

        if (isset($value)) {
            $elementData = $value;
        } else if (isset($this->formData[$name])) {
            $elementData = $this->formData[$name];
        }

        if (empty($initData)) {
            echo 'No initData for select element';
        }

        if ($type == 'multiple') {
            $name .= '[]';
            $params .= ' multiple = "multiple"';
        }

        $field = '<select name="' . $name . '" id="' . trim($name, '[]') . '" ' . $params . '>';

        foreach ($initData as $key => $value) {
            $selected = false;

            if (isset($elementData)) {
                if ($type == 'single') {
                    if ($elementData == $key) {
                        $selected = true;
                    }
                } elseif ($type == 'multiple') {
                    if (is_array($elementData) && in_array($key, $elementData)) {
                        $selected = true;
                    }
                }
            }

            if ($selected) {
                $str_selected = ' selected="selected"';
            } else {
                $str_selected = '';
            }

            $field .= '<option value="' . $key . '"' . $str_selected . '>' . $value . '</option>';
        }

        $field .= '</select>';

        return $field;
    }

    public function textarea($name, $width = 4, $height = 4, $params = '', $value = null) {
        if (isset($this->formData[$name])) {
            $elementData = htmlentities($this->formData[$name], ENT_QUOTES, 'UTF-8');
        }

        $field = '<textarea name="' . $name . '" id="' . $name . '" cols="' . $width . '" rows="' . $height . '" ' . $params . '>';

        if (isset($value)) {
            $field .= $value;
        } else if (isset($elementData)) {
            $field .= $elementData;
        }

        $field .= '</textarea>';

        return $field;
    }
    
    public function upload($name, $params = '') {
        $field = '<input name="' . $name . '" type="file" ' . $params . ' />';
        return $field;
    }

    public function submit($name, $value, $type = "submit", $params = '') {
        $field = '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" ' . $params . ' />';
        return $field;
    }
    
    public function button($name, $text = '', $type = "submit", $params = '') {
        $field = '<button type="' . $type . '" name="' . $name . '" ' . $params . '>';
        $field .= $text;
        $field .= '</button>';
        return $field;
    }
    
    public function button_image($src, $params = '') {
        $field = '<input type="image" src="' . $src . '" ' . $params . ' />';
        return $field;
    }

}