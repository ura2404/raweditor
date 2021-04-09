<?php
namespace Cmatrix;

class Cache {
    private $Name;
    private $Data;

    // --- --- --- --- ---
    function __construct($name,array $data=null){
        $this->Name = $name;
        $this->Data = $data;
        $this->createCache($data);
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return CM_ROOT.'/cache/'.$this->Name;
            case 'Data' : return $this->Data;
        }
    }

    // --- --- --- --- ---
    private function createCache($data=null){
        if(!file_exists($this->Path)){
            $Old = umask(0);
            mkdir($this->Path,0770,true);
            chown($this->Path,'www-data');
            chgrp($this->Path,'www-data');
            umask($Old);
        }

        if($data){
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
    static function create($name,array $data){
        return new self($name,$data);
    }

    // --- --- --- --- ---
    static function get($name){
        return new self($name);
    }

}
?>