<?php
namespace Cmatrix;
use \Cmatris as cm;

class Hash {
    private $Data = [];

    // --- --- --- --- ---
    function __construct($data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->Data;
        }
    }

    // --- --- --- --- ---
    public function getValue($url){
        $_rec = function($arr,$ini) use(&$_rec){
            if(count($arr) > 1){
                $Ind = array_shift($arr);
                return isset($ini[$Ind]) ? $_rec($arr,$ini[$Ind]) : false;
            }
            else return array_key_exists($arr[0],$ini) ? $ini[$arr[0]] : false;
        };
        return $_rec(explode('/',$url),$this->Data);
    }

    // --- --- --- --- ---
    public function setValue($url,$value){
        $_rec = function($arr,$ini) use(&$_rec,$value){
            if(count($arr) > 1){
                $Ind = array_shift($arr);
                $ini[$Ind] = $_rec($arr,is_array($ini[$Ind]) ? $ini[$Ind] : []);
            }
            else $ini[$arr[0]] = $value;

            return $ini;
        };

        $this->Data = $_rec(explode('/',trim($url,'/')),$this->Data);
        return $this;
    }

    // --- --- --- --- ---
    public function isExists($url){
        return !!$this->getValue($url);
    }

    // --- --- --- --- ---
    static function create($data){
        return new self($data);
    }
}
?>