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
        return htmlspecialchars(strAfter(\Cmatrix\App::$PAGE,'project/'));
        $Arr = explode('/',\Cmatrix\App::$PARAMS);
        return $Arr[0];
    }

    // --- --- --- --- ---
    private function getMyTree($name){
        $Path = \Cmatrix\Json::get(CM_ROOT.'/config.json')->Data['raweditor']['projects'][$name]['path'];
        return \Cmatrix\Dir::get($Path)->Tree;

        return [
            [
                'name' => '000.txt',
                'path' => '000.txt',
                'type' => 'folder',
                'level' => 0,
                'status' => 0,
                'children' => [
                    [
                        'name' => 'aaa.txt',
                        'path' => '/var/tmp/bbb.txt',
                        'type' => 'file',
                        'level' => 1,
                    ],
                    [
                        'name' => 'bbb.txt',
                        'path' => '/var/tmp/bbb.txt',
                        'type' => 'file',
                        'level' => 1,
                    ],
                    [
                        'name' => 'Qaz',
                        'path' => '/var/tmp/baaa',
                        'type' => 'folder',
                        'level' => 1,
                        'status' => 1,
                        'children' => [
                            [
                                'name' => 'dfdfdf.txt',
                                'path' => '/var/tmp/dfdfdf.txt',
                                'type' => 'file',
                                'level' => 2,
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => '111.txt',
                'path' => '111.txt',
                'type' => 'file',
                'level' => 0,
            ]
        ];
    }
}
?>