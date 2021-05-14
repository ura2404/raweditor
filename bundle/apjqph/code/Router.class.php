<?php
namespace Cmatrix;

class Router {
    static $ROUTERS = [];

    // --- --- --- --- ---
    function __construct($match,$data){
        if(!array_key_exists($match,self::$ROUTERS)) $this->createRouter($match,$data);
    }

    // --- --- --- --- ---
    private function createRouter($match,$data){
        self::$ROUTERS[$match] = array_merge([
            'match' => $match
        ],$data);
    }

    // --- --- --- --- ---
    static function add($match,array $data){
        return new self($match,$data);
    }
}
?>