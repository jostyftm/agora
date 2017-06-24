<?php 
namespace App\Config;

class Inflector{
    
    public static function camell($value){
        
        $segment = explode('-', $value);
        
        array_walk($segment, function(&$item){
            $item = ucfirst($item);
        });
        
        return implode('', $segment);
    }
    
    public static function lowercamell($value){
        return lcfirst(static::camell($value));
    }
}

