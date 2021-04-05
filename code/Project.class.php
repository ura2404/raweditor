<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Project {
    private $Data = [];


    // --- --- --- --- ---
    function __construct($name,$path=null){
        if($path) $this->createInstance($name,$path);
        else $this->getInstance($name);
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->Data;
            case 'Name' : return $this->Data ? $this->Data['name'] : false;
            case 'Path' : return $this->Data ? $this->Data['path'] : false;
        }
    }

    // --- --- --- --- ---
    private function createInstance($name,$path){
        $this->Data = [
            'name' => $name,
            'path' => $path
        ];
    }

    // --- --- --- --- ---
    private function getInstance($name){
        $Config = cm\Json::get(CM_ROOT.'/config.json')->Data;
        $this->Data = cm\Hash::create($Config)->getValue('raweditor/projects/'.$name);
        if(!$this->Data) throw new \Exception(cm\Local::get()->getValue('project/notExists'));
    }

    // --- --- --- --- ---
    public function add(){
        $Config = cm\Json::get(CM_ROOT.'/config.json')->Data;
        $Hash = cm\Hash::create($Config);

        array_map(function($project){
            if(
                ($project['name'] == $this->Data['name']) 
                || ($project['path'] == $this->Data['path'])
            ) throw new \Exception(cm\Local::get()->getValue('project/exists'));
        },$Hash->getValue('raweditor/projects/'));

        $Hash->setValue('raweditor/projects/'.$this->Name, $this->Data);
        cm\Json::create($Hash->Data)->put(CM_ROOT.'/config.json');
    }

    // --- --- --- --- ---
    public function delete(){
        $Config = cm\Json::get(CM_ROOT.'/config.json')->Data;
        $Hash = cm\Hash::create($Config);

        if(!$Hash->deleteValue('raweditor/projects/'.$this->Data['name'])) throw new \Exception(cm\Local::get()->getValue('project/notExists'));
        cm\Json::create($Hash->Data)->put(CM_ROOT.'/config.json');
    }

    // --- --- --- --- ---
    static function create($name, $path){
        $Path = realpath(CM_ROOT.$path);
        if(!$Path) throw new \Exception(cm\Local::get()->getValue('project/folderNotExists'));

        return new self($name,$Path);
    }

    // --- --- --- --- ---
    static function get($name){
        return new self($name);
    }

}
?>