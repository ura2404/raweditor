<?php
class Model {
    public function getData(){

        return [
            'version' => '1.0',
            'author' => 'ura@itx.ru',
            'home' => $this->getMyHome(),
            'projects' => $this->getMyProjects(),
            'local' => \Cmatrix\Local::get()->Data
        ];
    }

    // --- --- --- --- ---
    private function getMyHome(){
        return $_SERVER['REQUEST_SCHEME'] .'//'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }

    // --- --- --- --- ---
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