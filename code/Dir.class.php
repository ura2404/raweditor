<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Dir{
    private $Data;

    // --- --- --- --- ---
    function __construct(){
    }

    // --- --- --- --- ---
    static function get($path){

        if(!is_writable($path)) throw new \Exception(cm\Local::get()->Data['folderNotExists']);

        $Root = $path;

        $_rec = function($root=null) use($Root){
            $Dir = scandir($Root.'/'.$path);
            $Dir = array_filter($Dir,function($val){ return $val !== '.' && $val !== '..'; });
            usort($Dir,function($a,$b) use($Root,$root){
                $Path = $Root .'/' .($root ? $root. '/' : null). $a;
                return is_dir($Path) ?  -1 : 1;
            });
            dump($Dir);
        };

        $_rec();

    }
}
?>