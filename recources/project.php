<?php
header("Content-type: application/json");
require_once('../defs.php');
require_once('../common.php');

$Mode = isset($_POST['m']) ? $_POST['m'] : null;
if(!$Mode) die('Fuck off!!!');

$Json = \Cmatrix\Json::get(CM_ROOT.'/config.json');

$_add = function() use($Json){
    $Name = isset($_POST['name']) ? $_POST['name'] : null;
    $Path = isset($_POST['path']) ? $_POST['path'] : null;
    if(!$Name || !$Path) die('Fuck off!!!');

    \Cmatrix\Project::create($Name,$Path)->add();

    return [
        'message' => 'OK',
        'data' =>[
            'name' => $Name
        ]
    ];
};

$_del = function() use($Json){
    $Name = isset($_POST['name']) ? $_POST['name'] : null;
    if(!$Name) die('Fuck off!!!');

    \Cmatrix\Project::get($Name)->delete();

    return [
        'message' => 'OK',
        'data' =>[
            'name' => $Name
        ]
    ];
};

$_scan = function(){
    return [
        'stat' => [
            'js' => 10,
            'php' => 50,
            'less' => 30,
            'css3' => 5,
            'other' => 5
        ]
    ];
    $Name = isset($_POST['name']) ? $_POST['name'] : null;
    $Path = \Cmatrix\Project::get($Name)->Path;
};


try{
    switch($Mode){
        case 'add'  : $Ret = $_add();break;
        case 'del'  : $Ret = $_del();break;
        case 'scan' : $Ret = $_scan();break;
        default : die('Fuck off!!!');
    }

    echo json_encode(array_merge([
        'status' => 1
    ],$Ret));
}
catch(\Exception $e){
    echo json_encode([
        'status' => -1,
        'error' => $e->getMessage()
    ]);
}
?>