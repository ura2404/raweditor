<?php
namespace Cmatrix;

class Local {
    private $Lang;
    private $Data;

    // --- --- --- --- ---
    function __construct($lang){
        $this->Lang = $lang;
        $this->Data = $this->getMyData();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->Data;
        }
    }

    // --- --- --- --- ---
    private function getMyData(){
        $File = CM_ROOT .'/local.'. $this->Lang .'.json';
        return Json::get($File)->Data['local']['data'];
    }

    // --- --- --- --- ---
    static function get($lang='def'){
        $Config = json_decode(file_get_contents(CM_ROOT.'/config.json'),true);
        $Lang = isset($Config['raweditor']) && isset($Config['raweditor']['lang']) ? $Config['raweditor']['lang'] : $lang;
        return new self($Lang);
    }
}
?>