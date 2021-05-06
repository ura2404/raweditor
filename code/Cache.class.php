<?php
namespace Cmatrix;

class Cache {
    private $Name;
    private $Data;

    // --- --- --- --- ---
    function __construct($name){
        $this->Name = $name;
        $this->init();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return CM_ROOT.'/cache/'.$this->Name;
            case 'Data' : return $this->Data;
        }
    }

    // --- --- --- --- ---
    private function init(){
        if(!file_exists($this->Path)){
            $Old = umask(0);
            mkdir($this->Path,0770,true);
            chown($this->Path,'www-data');
            chgrp($this->Path,'www-data');
            umask($Old);
        }
    }

    // --- --- --- --- ---
    public function put($key,$value){
        return $this;
    }

    // --- --- --- --- ---
    public function putJson($key,array $data){
        Json::create($data)->put($this->Path.'/'.$key);
    }

    // --- --- --- --- ---
    public function getJson($key){
        return Json::get($this->Path.'/'.$key);
    }

    // --- --- --- --- ---
    static function create($name,$data){
        return (new self($name))->setData($data);
    }

    // --- --- --- --- ---
    static function get($name){
        $Cache = new self($name);

        //if(!file_exists($Cache->Path)) throw new \Exception(Local::get()->getValue('cache/notExists'));
        return $Cache;
    }

    // --- --- --- --- ---
    static function session(){
        $Tag = $_SERVER['REMOTE_ADDR'] . (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null);
        return new self(md5($Tag));
    }

}
?>