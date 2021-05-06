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
    private function getMyList(\Closure $_callback=null){
        $_callback = !$_callback ? function(){ return true; } : $_callback;
        
        $Dir = array_filter(scandir($this->Path,SCANDIR_SORT_ASCENDING),function($val){
            return $val !== '.' && $val !== '..';
        });
        
        usort($Dir,function($a,$b){
            return is_dir($this->Path.'/'.$a) ?  -1 : 1;
        });
        usort($Dir,function($a,$b){
            return is_dir($this->Path.'/'.$a) ?  -1 : 1;
        });
        
        $Dir = array_map(function($val) use($_callback){
            $Path = $this->Path.'/'.$val;
            $Data = [
                'name' => $val,
                'type' => is_dir($Path) ? 'folder' : 'file',
            ];
            
            $_callback($Data);
            return $Data;
        },$Dir);
        
        return $Dir;
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
                $Path = $path .'/'. $val;
                $Data = [
                    'parent' => strAfter($path,$this->Path),
                    'name' => $val,
                    'level' => $level,
                    'type' => is_dir($Path) ? 'folder' : (is_link($Path) ? 'link' : 'file'),
                ];
                
                //if($Data['type'] === 'file') $Data['size'] = filesize($Path);
                //if($Data['type'] === 'folder' && $_callback($Data)) $Data['children'] = $_rec($Path,($level+1));
                
                if($Data['type'] === 'file') $Data['size'] = filesize($Path);
                $Ret = $_callback($Data);
                if($Data['type'] === 'folder' && $Ret) $Data['children'] = $_rec($Path,($level+1));
                
                /*if($Data['type'] === 'folder'){
                    $Children = array_filter($_rec($Path,($level+1)),function($value){ return !!$value; });
                    if(count($Children)) $Data['children'] = $Children;
                }
                
                if(!$_callback($Data)) return;
                */
                
                return $Data;
            },$Dir);
            
            return $Dir;
        };
        
        $Tree = $_rec($this->Path);
        return $Tree;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getTree(\Closure $_callback=null){
        return $this->getMyTree($_callback);
    }

    // --- --- --- --- ---
    public function getList(\Closure $_callback=null){
        return $this->getMyList($_callback);
    }

    // --- --- --- --- ---
    public function getTypes(\Closure $_callback=null){
        return $this->getMyTypes($_callback);
    }
    
    // --- --- --- --- ---
    static function get($path){
        if(!is_dir($path)) throw new \Exception(cm\Local::get()->Data['folderNotExists']);
        if(!is_writable($path)) throw new \Exception(cm\Local::get()->Data['folderNotAccessable']);
        return new self($path);
    }
}
?>