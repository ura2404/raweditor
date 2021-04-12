<?php
namespace Cmatrix\Models;

class Project extends Common {
    public function getData(){

        $Name = $this->getMyName();

//dump($this->getMyTree($Name));die();

        return arrayMergeReplace(parent::getData(),[
            'name' => $Name,
            'tree' => $this->getMyTree($Name),
        ]);
    }

    // --- --- --- --- ---
    private function getMyName(){
        return strAfter(\Cmatrix\App::$PAGE,'project/');
        return htmlspecialchars(strAfter(\Cmatrix\App::$PAGE,'project/'));
        $Arr = explode('/',\Cmatrix\App::$PARAMS);
        return $Arr[0];
    }

    // --- --- --- --- ---
    private function getMyTree($name){
        $Path = \Cmatrix\Project::get($name)->Path;
        $Dir = \Cmatrix\Dir::get($Path);
        $Tree = $Dir->getTree(function(&$item){
            $item['hid'] = hid($item['parent'].$item['name']);
            return $item['level'] < 2 ? true : false;
        });
        \Cmatrix\Cache::session()->putJson('tree',$Tree);

        return $Tree;

        $Hash = \Cmatrix\Hash::create($Tree);

//dump($Dir->Tree);
dump($Hash);
die();

        $Tree = $Dir->getTree(function(&$item){
            $item['hid'] = hid($item['parent'].$item['name']);
            if($item['level'] < 2) return true;
            return false;

            //dump($name,$dir);
            //return strpos('www',$dir)!==false;
            //return $name == 'www';
            //return $name != '.git';
        });

        //\Cmatrix\Cache::get('tree');
        \Cmatrix\Cache::create('tree',$Tree)->flush()->getValue();



        //dump(\Cmatrix\Cache::get('tree')->Data);

        //\Cmatrix\Cache::create('tree')->put($Tree);
        return $Tree;
    }
}
?>