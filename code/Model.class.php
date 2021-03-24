<?php
namespace Cmatrix;

class Model {
    private $ClassName;

    // --- --- --- --- ---
    function __construct($name){
        $this->ClassName = $this->init($name);
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Data' : return $this->getMyData();
        }
    }

    // --- --- --- --- ---
    private function init($name){
        $FilePath = CM_ROOT.'/models/'.$name.'.php';

        $_content = function($name) use($FilePath){
            $Text = trim(file_get_contents($FilePath));

            $Pos1 = strpos($Text,'Model');
            $Pos2 = strrpos($Text,'}');

            $Text = substr($Text,$Pos1,$Pos2-$Pos1+1);
            $Text = str_replace('Model',$name,$Text);

            eval('class '. $Text);
        };

        $ClassName = $name.'__model__';
        $_content($ClassName);

        return $ClassName;
    }

    // --- --- --- --- ---
    private function getMyData(){
        return (new $this->ClassName())->getData();
    }

    // --- --- --- --- ---
    static function get($name){
        return new self($name);
    }
}
?>