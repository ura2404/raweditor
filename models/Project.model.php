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
        $Cache = \Cmatrix\Cache::session()->folder('tree-'.$name);
        
        $Path = \Cmatrix\Project::get($name)->Path;
        $Dir = \Cmatrix\Dir::get($Path);
        
        $Tree = $Dir->getTree(function(&$item) use($Cache){
            $Url = $item['parent'].'/'.$item['name'];
            $item['hid'] = hid($Url);
            $Cache->putValue(str_replace('/','_',$Url),$item['hid']);
            
            return $item['level'] < 1 ? true : false;
        });
        \Cmatrix\Cache::session()->putJson('tree-'.$name,$Tree);
        
//dump($Tree);die();

        return $Tree;
    }
}
?>