<?php

class MySort {

    public static function sortObject($array, $field, $direction = 'asc') {
        for($i=0; $i < count($array) - 1; $i++) {
            for($j=$i+1; $j < count($array); $j++) {
                if(is_numeric($array[$i]->$field) && is_numeric($array[$j]->$field)) {
                    if($direction == 'asc') {
                        if($array[$i]->$field > $array[$j]->$field) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    } else {
                        if($array[$i]->$field < $array[$j]->$field) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    }
                } else {
                    if($direction == 'asc') {
                        if(strcmp($array[$i]->$field, $array[$j]->$field) > 0) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    } else {
                        if(strcmp($array[$j]->$field, $array[$i]->$field) > 0) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    }
                }
            }
        }
        
        return $array;
    }
    
    public static function extrasObject($array, $field, $value_field) {
        $result = [];
        
        foreach ($array as $value) {
            if($value->$field == $value_field) {
                $result[] = $value;
            }
        }
        
        return $result;
    }
    
    public static function sortArray($array, $field, $direction = 'asc') {
        for($i=0; $i < count($array) - 1; $i++) {
            for($j=$i+1; $j < count($array); $j++) {
                if(is_numeric($array[$i][$field]) && is_numeric($array[$j][$field])) {
                    if($direction == 'asc') {
                        if($array[$i][$field] > $array[$j][$field]) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    } else {
                        if($array[$i][$field] < $array[$j][$field]) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    }
                } else {
                    if($direction == 'asc') {
                        if(strcmp($array[$i][$field], $array[$j][$field]) > 0) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    } else {
                        if(strcmp($array[$j][$field], $array[$i][$field]) > 0) {
                            $temp = $array[$j];
                            $array[$j] = $array[$i];
                            $array[$i] = $temp;
                        }
                    }
                }
            }
        }
        
        return $array;
    }

    public static function extrasArray($array, $field, $value_field) {
        $result = [];
        
        foreach ($array as $value) {
            if($value[$field] == $value_field) {
                $result[] = $value;
            }
        }
        
        return $result;
    }
    
}