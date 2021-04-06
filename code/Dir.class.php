<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Dir{
    private $Path;
    private $Data;

    // --- --- --- --- ---
    function __construct($path){
        $this->Path = $path;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Tree' : return $this->getMyTree();
        }
    }

    // --- --- --- --- ---
    private function getMyTree(\Closure $_callback=null){
        $_rec = function($path) use(&$_rec,$_callback){
            $Dir = scandir($path);

            $Dir = array_filter($Dir,function($val) use($_callback){
                $_callback
                return $val !== '.' && $val !== '..' && $_callback ? $_callback($Dir,$val);
            });

            usort($Dir,function($a,$b) use($path){
                return is_dir($path.'/'.$a) ?  -1 : 1;
            });

            $Dir = array_map(function($val) use($path,&$_rec){
                //$Children = is_dir($path.'/'.$val) ? $_rec($path.'/'.$val) : null;
                $Data = [
                    'name' => $val,
                    'type' => is_dir($path.'/'.$val) ? 'folder' : 'file'
                ];

                //if($Children !== null) $Data['children'] = $Children;
                return $Data;
            },$Dir);
            return $Dir;
        };

        $Data = $_rec($this->Path);
        dump($Data);
    }

    // --- --- --- --- ---
    public function getTree(\Closure $_callback=null){
        return $this->getMyTree($_callback);
    }

    // --- --- --- --- ---
    static function get($path){
        if(!is_writable($path)) throw new \Exception(cm\Local::get()->Data['folderNotExists']);
        return new self($path);
    }
}
?>