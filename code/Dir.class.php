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
    private function getItems($path,\Closure $_callback=null){
        $Dir = scandir($path);

        $Dir = array_filter($Dir,function($val) use($path,$_callback){
            return $val !== '.' && $val !== '..' && ($_callback ? $_callback($path,$val) === true : true);
        });

        usort($Dir,function($a,$b) use($path){
            return is_dir($path.'/'.$a) ?  -1 : 1;
        });

        $Dir = array_map(function($val) use($path,&$_rec){
            return [
                'parent' => $path,
                'name' => $val,
                'type' => is_dir($path.'/'.$val) ? 'folder' : 'file'
            ];
        },$Dir);

        return $Dir;
    }

    // --- --- --- --- ---
    private function getMyList(\Closure $_callback=null){
        return $this->getItems($this->Path, $_callback);
    }

    // --- --- --- --- ---
    private function getMyTree(\Closure $_callback=null){
        $_rec = function($path,$level=0) use($_callback,&$_rec){
            $Dir = scandir($path);
            $Dir = array_filter($Dir,function($val) use($path,$_callback){
                return $val !== '.' && $val !== '..';
            });
            usort($Dir,function($a,$b) use($path){
                return is_dir($path.'/'.$a) ?  -1 : 1;
            });
            $Dir = array_map(function($val) use($path,$level,$_callback,&$_rec){
                $Path = $path.'/'.$val;
                $Data = [
                    'parent' => $path,
                    'name' => $val,
                    'level' => $level,
                    'type' => is_dir($Path) ? 'folder' : 'file',
                ];

                if($Data['type'] !== 'folder') $Data['size'] = filesize($Path);
                if($Data['type'] === 'folder') if($_callback($Data) !== false) $Data['children'] = $_rec($Path,($level+1));

                return $Data;
            },$Dir);

            return $Dir;
        };

        return $_rec($this->Path);
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