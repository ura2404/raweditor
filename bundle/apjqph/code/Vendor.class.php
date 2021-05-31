<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Vendor {
    
    // --- --- --- --- ---
    function __construct($data){
        $this->Data = $data;
    }
    
    // --- --- --- --- ---
    static function reg($code){
        $Path = CM_ROOT .'/code/vendor/'. $code .'.php';
        if(!file_exists($Path)) throw new cm\Exception('нет такого vendor модуля.');
        dump($Path);
        require_once($Path);
    }
}
?>