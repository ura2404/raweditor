<?php
namespace Cmatrix;

class Page {
    private $Name;
    private $Twig;

    // --- --- --- --- ---
    function __construct($name){
        $this->Name = $name;
        $this->init();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Html' : return $this->getMyHtml();
        }
    }

    // --- --- --- --- ---
    private function init(){
        $this->Controller = CM_ROOT.'/controllers/'.$this->Name.'.php';

        $Loader = new \Twig_Loader_Filesystem(CM_ROOT.'/templates/');

        $this->Twig = new \Twig_Environment($Loader, [
            'cache' => '/var/tmp',
            'debug' => true,
            'auto_reload' => true
        ]);
    }

    // --- --- --- --- ---
    private function getMyHtml(){
        return $this->Twig->render($this->Name.'.twig',\Cmatrix\Model::get($this->Name)->Data);
    }

    // --- --- --- --- ---
    static function get($name){
        return new self($name);
    }
}
?>