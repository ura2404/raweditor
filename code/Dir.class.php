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
            case 'List' : return $this->getMyList();
        }
    }

    // --- --- --- --- ---
    private function getMyTree(\Closure $_callback=null){
        $_callback = !$_callback ? function(){ return true; } : $_callback;
        $_rec = function($path,$level=0) use($_callback,&$_rec){
            $Dir = array_filter(scandir($path,SCANDIR_SORT_ASCENDING),function($val){
                return $val !== '.' && $val !== '..';
            });

            //asort($Dir);
            //sort($Dir,SORT_STRING);

            usort($Dir,function($a,$b) use($path){
                return is_dir($path.'/'.$a) ?  -1 : 1;
            });
            usort($Dir,function($a,$b) use($path){
                return is_dir($path.'/'.$a) ?  -1 : 1;
            });

            $Dir = array_map(function($val) use($path,$level,$_callback,&$_rec){
                $Path = $path.'/'.$val;
                $Data = [
                    'parent' => strAfter($path,$this->Path),
                    'name' => $val,
                    'level' => $level,
                    'type' => is_dir($Path) ? 'folder' : 'file',
                ];

                if($Data['type'] !== 'folder'){
                    $Data['size'] = filesize($Path);
                    $_callback($Data);
                }

                if($Data['type'] === 'folder'){
                    if($_callback($Data)){
                        $Data['children'] = $_rec($Path,($level+1));
                    }
                }

                /*if($_callback($Data)){
                    if($Data['type'] === 'folder') $Data['children'] = $_callback($Data) !== false ? $_rec($Path,($level+1)) : [];
                }*/

                return $Data;
            },$Dir);

            return $Dir;
        };

        $Tree = $_rec($this->Path);
        return $Tree;
    }

    // --- --- --- --- ---
    public function getTree(\Closure $_callback=null){
        return $this->getMyTree($_callback);
    }

    // --- --- --- --- ---
    static function get($path){
        if(!is_dir($path) || !is_writable($path)) throw new \Exception(cm\Local::get()->Data['folderNotExists']);
        return new self($path);
    }
}
?>