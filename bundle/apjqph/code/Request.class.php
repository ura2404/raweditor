<?php
namespace Cmatrix;

class Request {
    private $Data;

    // --- --- --- --- ---
    function __construct(){
        $this->init();
    }

    // --- --- --- --- ---
    private function init(){
        $Data = file_get_contents('php://input');
        parse_str($Data,$this->Data);
    }
    
    // --- --- --- --- ---
    public function getValue($key,$def=null){
        return isset($this->Data[$key]) ? $this->Data[$key] : $def;
    }
    
    // --- --- --- --- ---
    static function get(){
        return new self();
    }
    
    // --- --- --- --- ---
    static function value($key,$def=null){
        return self::get()->getValue($key,$def);
    }
}
?>