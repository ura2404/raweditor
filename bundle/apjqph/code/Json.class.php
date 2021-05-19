<?php
namespace Cmatrix;

class Json {
    private $Data;

    // --- --- --- --- ---
    function __construct(array $data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->getData();
        }
    }

    // --- --- --- --- ---
    private function getData(){
        return $this->Data;
    }

    // --- --- --- --- ---
    public function setData(array $data){
        $this->Data = $data;
        return $this;
    }

    // --- --- --- --- ---
    public function put($filePath){
        file_put_contents($filePath,json_encode($this->Data,
            JSON_PRETTY_PRINT             // форматирование пробелами
            | JSON_UNESCAPED_SLASHES      // не экранировать '/'
            | JSON_UNESCAPED_UNICODE      // не кодировать текст
         ));
    }

    // --- --- --- --- ---
    static function create(array $Data){
        return new self($Data);
    }

    // --- --- --- --- ---
    static function get($path){
        if(!file_exists($path)) throw new \Exception('Wrong json file');
        $Arr = json_decode(file_get_contents($path),true);
        return new self($Arr);
    }
}
?>