<?php
header("Content-type: application/json");
require_once('../defs.php');
require_once('../common.php');

$Mode = isset($_POST['m']) ? $_POST['m'] : null;
if(!$Mode) die('Fuck off!!!');

$Json = \Cmatrix\Json::get(CM_TOP.'/config.json');

$_add = function() use($Json){
    if(!($Name = \Cmatrix\Request::value('name'))) die('Fuck off!!!');
    if(!($Path = \Cmatrix\Request::value('path'))) die('Fuck off!!!');

    \Cmatrix\Project::create($Name,$Path)->add();

    return [
        'message' => 'OK',
        'data' =>[
            'name' => $Name
        ]
    ];
};

$_del = function() use($Json){
    if(!($Name = \Cmatrix\Request::value('name'))) die('Fuck off!!!');

    \Cmatrix\Project::get($Name)->delete();

    return [
        'message' => 'OK',
        'data' =>[
            'name' => $Name
        ]
    ];
};

$_scan = function(){
    if(!($Name = \Cmatrix\Request::value('name'))) die('Fuck off!!!');
    
    return [
        'stat' => \Cmatrix\Project::get($Name)->scan()
    ];
};

// ---- ---- ---- --- ----
// ---- ---- ---- --- ----
// ---- ---- ---- --- ----
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