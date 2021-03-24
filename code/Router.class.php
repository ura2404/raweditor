<?php
namespace Cmatrix;

class Router {
    static $ROUTERS = [];

    // --- --- --- --- ---
    function __construct($path,$data){
        if(!array_key_exists($path,self::$ROUTERS)) $this->createRouter($path,$data);
    }

    // --- --- --- --- ---
    private function createRouter($path,$data){
        self::$ROUTERS[$path] = $data;
    }

    // --- --- --- --- ---
    static function add($path,array $data){
        return new self($path,$data);
    }
}
?>