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

        return $_rec(explode('/',trim($url,'/')),$this->Data);
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
    public function getRuleValue(array $rule){
        $Key = array_keys($rule)[0];
        $_rec = function(array $data) use($Key,$rule,&$_rec){
            foreach($data as $key=>$value){
                if(count(array_intersect_key($value,$rule)) && $value[$Key] === $rule[$Key]){
                    return $value;
                }elseif(isset($value['children'])){
                    if($ret = $_rec($value['children'])) return $ret;
                }

            }
            return false;
        };
        return $_rec($this->Data);
    }

    // --- --- --- --- ---
    public function deleteValue($url){
        $Success = false;

        $_rec = function($arr,$ini) use(&$_rec,&$Success){
            if(count($arr) > 1){
                $Ind = array_shift($arr);
                isset($ini[$Ind]) ? $ini[$Ind] = $_rec($arr,$ini[$Ind]) : null;
                //return isset($ini[$Ind]) ? $_rec($arr,$ini[$Ind]) : false;
                //$ini[$Ind] = $_rec($arr,is_array($ini[$Ind]) ? $ini[$Ind] : []);
            }
            else{
                if(array_key_exists($arr[0],$ini)){
                    $Success = true;
                    unset($ini[$arr[0]]);
                }
            }
            return $ini;
        };

        $this->Data = $_rec(explode('/',trim($url,'/')),$this->Data);
        return $Success;
    }

    // --- --- --- --- ---
    public function isExists($url){
        return !!$this->getValue($url);
    }

    // --- --- --- --- ---
    static function create($data){
        return new self($data);
    }

    // --- --- --- --- ---
    static function get($path){
        return new self(Json::get($path)->Data);
    }
}
?>