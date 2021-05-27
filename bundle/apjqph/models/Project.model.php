<?php
namespace Cmatrix\Models;

class Project extends Common {
    public function getData(){
        $Name = $this->getMyName();
        
        return arrayMergeReplace(parent::getData(),[
            'name' => $Name,
            'tree' => $this->getMyTree($Name),
        ]);
    }

    // --- --- --- --- ---
    private function getMyName(){
        return strAfter(\Cmatrix\App::$PAGE,'project/');
        //return htmlspecialchars(strAfter(\Cmatrix\App::$PAGE,'project/'));
        //$Arr = explode('/',\Cmatrix\App::$PARAMS);
        //return $Arr[0];
    }

    // --- --- --- --- ---
    private function getMyTree($name){
        $Cache = \Cmatrix\Cache::session()->touchFolder('tree-'.$name);
        
        $Path = \Cmatrix\Project::get($name)->Path;
        $Dir = \Cmatrix\Dir::get($Path);
        
        $Tree = $Dir->getTree(function(&$item) use($Cache){
            $item['hid'] = hid($item['parent'].'/'.$item['name']);
            
            $Cache->putJson($item['hid'],[
                'name' => $item['name'],
                'parent' => $item['parent'],
                'level' => $item['level']
            ]);
            
            return $item['level'] < 1 ? true : false;
        });
        
        return $Tree;
    }
}
?>