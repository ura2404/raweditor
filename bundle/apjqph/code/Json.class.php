<?php
namespace Cmatrix;

class Json {
    /**
     * data array
     */
    private $Data;

    // --- --- --- --- ---
    function __construct(array $data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->getData();
            case 'Encode' : return $this->encode();
            
        }
    }

    // --- --- --- --- ---
    private function getData(){
        return $this->Data;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return string 
     */
    public function encode(){
        return json_encode($this->Data,
            JSON_PRETTY_PRINT             // форматирование пробелами
            | JSON_UNESCAPED_SLASHES      // не экранировать '/'
            | JSON_UNESCAPED_UNICODE      // не кодировать текст
        );
    }

    
    
    
    
    // --- --- --- --- ---
    /**
     * @return array
     */
     /*
    public function decode(){
        return $this->Data;
    }
*/    

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

    static function getString($data){
        $Arr = json_decode($data,true);
        return new self($Arr);
    }
    
    // --- --- --- --- ---
    /**
     * @param string $path - путь к json файлу
     */
    static function getFile($path){
        if(!file_exists($path)) throw new \Exception('Wrong json file');
        $Arr = json_decode(file_get_contents($path),true);
        return new self($Arr);
    }
}
?>