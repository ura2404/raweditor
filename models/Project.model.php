<?php
namespace Cmatrix\Models;

class Project extends Common {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'name' => $this->getMyName(),
//            'version' => '1.0',
//            'author' => 'ura@itx.ru',
//            'home' => $this->getMyHome(),
//            'projects' => $this->getMyProjects(),
//            'local' => \Cmatrix\Local::get()->Data
        ]);
    }

    // --- --- --- --- ---
    private function getMyName(){
        return strAfter(\Cmatrix\App::$PAGE,'project/');
        return htmlspecialchars(strAfter(\Cmatrix\App::$PAGE,'project/'));
        $Arr = explode('/',\Cmatrix\App::$PARAMS);
        return $Arr[0];
    }
}
?>