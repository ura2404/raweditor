<?php
namespace Cmatrix\Models;

class Main extends Common {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'projects' => $this->getMyProjects(),
        ]);
    }

    // --- --- --- --- ---
    private function getMyProjects(){
        $Config = json_decode(file_get_contents(CM_TOP.'/config.json'),true);

        return array_map(function($project){
            return [
                'name' => $project['name']
            ];
        },$Config['raweditor']['projects']);
    }
}
?>