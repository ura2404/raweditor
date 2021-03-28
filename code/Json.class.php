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
            | JSON_UNESCAPED_SLASHES      // не экранировать /
            | JSON_UNESCAPED_UNICODE      // не кодировать текст
         ));
    }

    // --- --- --- --- ---
    static function get($filePath){
        if(!file_exists($filePath)) throw new \Exception('Wrong json file');
        $Arr = json_decode(file_get_contents($filePath),true);
        return new self($Arr);
    }

}
?>