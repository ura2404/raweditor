<?php
namespace Cmatrix\Models;

class Project extends Common {
    public function getData(){
        $Project = $this->getMyProjectName();
        
        return arrayMergeReplace(parent::getData(),[
            'project' => $Project,
            'tree' => $this->getMyTree($Project),
        ]);
    }

    // --- --- --- --- ---
    private function getMyProjectName(){
        return strAfter(\Cmatrix\App::$PAGE,'project/');
        //return htmlspecialchars(strAfter(\Cmatrix\App::$PAGE,'project/'));
        //$Arr = explode('/',\Cmatrix\App::$PARAMS);
        //return $Arr[0];
    }

    // --- --- --- --- ---
    private function getMyTree($project){
        $Cache = \Cmatrix\Cache::session()->touchFolder('tree-'.$project);
        
        $Path = \Cmatrix\Project::get($project)->Path;
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