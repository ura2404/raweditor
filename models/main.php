<?php
class Model {
    public function getData(){

        return [
            'version' => '1.0',
            'author' => 'ura@itx.ru',
            'projects' => $this->getMyProjects(),
            'local' => \Cmatrix\Local::get()->Data
        ];
    }

    private function getMyProjects(){
        $Config = json_decode(file_get_contents(CM_ROOT.'/config.json'),true);
        return array_map(function($project){
            return [
                'name' => $project['name']
            ];
        },$Config['raweditor']['projects']);
    }
}
?>