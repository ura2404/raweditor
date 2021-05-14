<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Project {
    private $Data = [];

    /**
     * @param string $name - имя проекта
     * @param string $path - путь к проекту
     */
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
        $Config = cm\Json::get(CM_TOP.'/config.json')->Data;
        $this->Data = cm\Hash::create($Config)->getValue('raweditor/projects/'.$name);
        if(!$this->Data) throw new \Exception(cm\Local::get()->getValue('project/notExists'));
    }

    // --- --- --- --- ---
    public function add(){
        $Config = cm\Json::get(CM_TOP.'/config.json')->Data;
        $Hash = cm\Hash::create($Config);

        array_map(function($project){
            if(
                ($project['name'] == $this->Data['name']) 
                || ($project['path'] == $this->Data['path'])
            ) throw new \Exception(cm\Local::get()->getValue('project/exists'));
        },$Hash->getValue('raweditor/projects/'));

        $Hash->setValue('raweditor/projects/'.$this->Name, $this->Data);
        cm\Json::create($Hash->Data)->put(CM_TOP.'/config.json');
    }

    // --- --- --- --- ---
    public function delete(){
        $Config = cm\Json::get(CM_TOP.'/config.json')->Data;
        $Hash = cm\Hash::create($Config);
        
        if(!$Hash->deleteValue('raweditor/projects/'.$this->Data['name'])) throw new \Exception(cm\Local::get()->getValue('project/notExists'));
        cm\Json::create($Hash->Data)->put(CM_TOP.'/config.json');
    }

    // --- --- --- --- ---
    public function scan(){
        $Valids = ['php','js','html','css','less','scss','xml','xsl','py','c','pl','twig','md','sql'];
        $Trans = [
            'c++' => 'c',
            'h' => 'c',
            'xhtml' => 'html',
        ];
        $Res = [];
        
        Dir::get($this->Path)->getTree(function($item) use($Valids,&$Res) {
            if(
                $item['type'] === 'file'
                && strpos($item['name'],'.') !== 0
                && strpos($item['parent'],'/.git') === false
                && strpos($item['parent'],'/cache') === false
            ){
                $Info = new \SplFileInfo($item['name']);
                $Ext = strtolower($Info->getExtension());
                if(in_array($Ext,$Valids)) isset($Res[$Ext]) ? $Res[$Ext]++ : $Res[$Ext] = 1;
            }
            return true;
        });
        
        $Total = array_sum($Res);
        $Res = array_map(function($val) use($Total){
            $Val = round($val/$Total*100,0/*,PHP_ROUND_HALF_UP*/);
            return $Val;
            //return $Val < 1 ? '<1' : $Val;
        },$Res);
        //$Res = array_filter($Res,function($val){ return $val > 0.1; });
        arsort($Res);
        
        return $Res;
        
        return Dir::get($this->Path)->getTypes(function($item){
            return (
                //strpos($item['name'],'.git') === 0 
                strpos($item['name'],'.') === 0
                || strpos($item['parent'],'/.git') !== false
                || strpos($item['parent'],'/cache') !== false
            ) ? false : true;
            //dump($item);
            return true;
        });
    }

    // --- --- --- --- ---
    /**
     * @param string $name - имя проекта
     * @param string $path - путь к проекту
     */
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