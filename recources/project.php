<?php
header("Content-type: application/json");
require_once('../www/common.php');

$Mode = isset($_POST['m']) ? $_POST['m'] : null;
if(!$Mode) die('Fuck off!!!');

$Json = \Cmatrix\Json::get(CM_ROOT.'/config.json');
$Data = $Json->Data;

$_add = function() use($Json,$Data){
    $Name = isset($_POST['name']) ? $_POST['name'] : null;
    $Path = isset($_POST['path']) ? $_POST['path'] : null;
    if(!$Name || !$Path) die('Fuck off!!!');

    $Path = realpath($Path);
    if(!$Path) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['wrongPath']));

    array_map(function($project) use($Path){
        if($project['path'] == $Path) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['projectExists']));
    },$Data['raweditor']['projects']);

    $Data['raweditor']['projects'][] = [
        'name' => $Name,
        'path' => $Path
    ];

    $Json->setData($Data);
    $Json->put(CM_ROOT.'/config.json');
    return [
        'name' => $Name
    ];
};

$_del = function() use($Json,$Data){
    return $data;
};



try{
    switch($Mode){
        case 'add' : $Ret = $_add();break;
        case 'del' : $Ret = $_del();break;
    }


    /*
    $Path = realpath($Path);
    if(!$Path) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['wrongPath']));

    $FilePath = CM_ROOT .'/config.json';
    $Json = \Cmatrix\Json::get($FilePath);
    $Data = $Json->Data;

    array_map(function($project) use($Path){
        if($project['path'] == $Path) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['projectExists']));
    },$Data['raweditor']['projects']);

    $Data['raweditor']['projects'][] = [
        'name' => $Name,
        'path' => $Path
    ];

    $Json->setData($Data);
    $Json->put($FilePath);
    */

    echo json_encode([
        'status' => 1,
        'data' => $Ret
    ]);
}
catch(\Exception $e){
    echo json_encode([
        'status' => -1,
        'error' => $e->getMessage()
    ]);
}
?>