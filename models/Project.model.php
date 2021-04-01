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
        return [
            [
                'name' => '000.txt',
                'path' => '000.txt',
                'type' => 'folder',
                'status' => 0,
                'children' => [
                    [
                        'name' => 'aaa.txt',
                        'path' => '/var/tmp/bbb.txt',
                        'type' => 'file'
                    ],
                    [
                        'name' => 'bbb.txt',
                        'path' => '/var/tmp/bbb.txt',
                        'type' => 'file'
                    ],
                    [
                        'name' => 'Qaz',
                        'path' => '/var/tmp/baaa',
                        'type' => 'folder',
                        'status' => 1,
                        'children' => [
                            [
                                'name' => 'dfdfdf.txt',
                                'path' => '/var/tmp/dfdfdf.txt',
                                'type' => 'file'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => '111.txt',
                'path' => '111.txt',
                'type' => 'file'
            ]
        ];
    }
}
?>