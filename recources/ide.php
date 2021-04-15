<?php
header("Content-type: application/json");
require_once('../defs.php');
require_once('../common.php');

$Mode = isset($_POST['m']) ? $_POST['m'] : null;
if(!$Mode) die('Fuck off!!!');

//$Json = \Cmatrix\Json::get(CM_ROOT.'/config.json');
/*
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
*/

$_node = function(){
    $Name = isset($_POST['name']) ? $_POST['name'] : null;
    $Hid = isset($_POST['hid']) ? $_POST['hid'] : null;
    if(!$Name || !$Hid) die('Fuck off!!!');

    //$Path = \Cmatrix\Project::get($Name)->Path;

    $Tree = \Cmatrix\Cache::session()->getJson('tree-'.$Name);
    $Hash = \Cmatrix\Hash::create($Tree->Data);
    $Node = $Hash->getRuleValue(['hid'=>$Hid]);
    if(!$Node) die('Fuck off!!!');

    $Path = \Cmatrix\Project::get($Name)->Path . $Node['parent'] .'/'. $Node['name'];
    $Dir = \Cmatrix\Dir::get($Path);
    $List = $Dir->getList(function(&$item) use($Node){
        $item['hid'] = hid($Node['parent'].'/'.$Node['name'].'/'.$item['name']);
        $item['parent'] = $Node['parent'].'/'.$Node['name'];
        $item['level'] = $Node['level'] + 1;
    });

    return [
        'message' => 'OK',
        'data' => [
            'name' => $Name,
            'list' => $List
        ]
    ];
};


try{
    switch($Mode){
//        case 'add'  : $Ret = $_add();break;
//        case 'del'  : $Ret = $_del();break;
        case 'node' : $Ret = $_node();break;
    }

    echo json_encode(array_merge([
        'status' => 1
    ],$Ret));
}
catch(\Exception $e){
    echo json_encode([
        'status' => -1,
        'message' => $e->getMessage()
    ]);
}
?>