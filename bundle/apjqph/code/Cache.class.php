<?php
namespace Cmatrix;
use Cmatrix as cm;

class Cache {
    private $Name;
    private $Data;
    private $Folde;

    // --- --- --- --- ---
    function __construct($name){
        $this->Name = $name;
        $this->init();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return CM_TOP.'/cache/'.$this->Name.($this->Folder ? '/'.$this->Folder : null);
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
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function folder($name){
        $this->Folder = $name;
        if(!file_exists($this->Path)){
            $Old = umask(0);
            mkdir($this->Path,0770,true);
            chown($this->Path,'www-data');
            chgrp($this->Path,'www-data');
            umask($Old);
        }
        return $this;
    }    

    // --- --- --- --- ---
    public function delFolder($name){
        $this->Folder = $name;
        if(!file_exists($this->Path)) throw new \Exception(cm\Local::getVal('folder/notExists'));
        
        $_rec = function($path) use(&$_rec){
            if(is_file($path)) unlink($path);
            else{
                array_map(function($name) use(&$_rec,$path){
                    if($name !== '.' && $name !== '..') $_rec($path.'/'.$name);
                },scandir($path));
                rmdir($path);
            }
        };
        $_rec($this->Path);
        
        return $this;
    }    

    public function putValue($key,$value){
        file_put_contents($this->Path.'/'.$key,$value);
        return $this;
    }

    // --- --- --- --- ---
    public function getValue($key){
        $Path = $this->Path.'/'.$key;
        if(!file_exists($Path)) throw new \Exception(cm\Local::getVal('file/notExists'));
        return file_get_contents($Path);
    }

    // --- --- --- --- ---
    public function delValue($key){
        $Path = $this->Path.'/'.$key;
        if(!file_exists($Path)) throw new \Exception(cm\Local::getVal('file/notExists'));
        unlink($Path);
        return $this;
    }

    // --- --- --- --- ---
    public function putJson($key,array $data){
        Json::create($data)->put($this->Path.'/'.$key);
        return $this;
    }

    // --- --- --- --- ---
    public function getJson($key){
        return Json::get($this->Path.'/'.$key);
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function create($name,$data){
        return (new self($name))->setData($data);
    }

    // --- --- --- --- ---
    static function get($name){
        return new self($name);
    }

    // --- --- --- --- ---
    static function session(){
        $Tag = $_SERVER['REMOTE_ADDR'] . (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null);
        return new self(md5($Tag));
    }

}
?>