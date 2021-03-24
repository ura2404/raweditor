<?php
namespace Cmatrix;

class App {
    private $PageName;
    private $Twig;

    // --- --- --- --- ---
    function __construct($pageName=null){
        $this->PageName = $pageName ? $pageName : $this->probePage();

        $this->Twig = new \Twig_Environment(new \Twig_Loader_Filesystem(CM_ROOT.'/templates/'), [
            'cache' => '/var/tmp',
            'debug' => true,
            'auto_reload' => true
        ]);
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Html' : return $this->getMyHtml();
        }
    }

    // --- --- --- --- ---
    private function probePage(){
        $Page = strAfter(trim(rtrim($_SERVER['REDIRECT_QUERY_STRING'],'/')),'cmp=');
        $Page = $Page == '' ? '/' : $Page;
        return $Page;
    }

    // --- --- --- --- ---
    private function getMyHtml(){
        $Router = isset(\Cmatrix\Router::$ROUTERS[$this->PageName]) ? \Cmatrix\Router::$ROUTERS[$this->PageName] : null;
        if(!$Router || !isset($Router['template'])) return;

        $Template = $Router['template'].'.twig';

        $Model = isset($Router['model']) ? $Router['model'] : [];
        $Data = $Model instanceof \Closure ? $Model() : \Cmatrix\Model::get($Model)->Data;

        return $this->Twig->render($Template,$Data);
    }

    // --- --- --- --- ---
    static function get($pageName=null){
        return new self($pageName);
    }
}
?>