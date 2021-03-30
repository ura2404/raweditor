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

    $Path = realpath(CM_ROOT.$Path);
    if(!$Path) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['wrongPath']));

    $Data = $Json->Data;

    array_map(function($project) use($Path,$Name){
        if($project['path'] == $Path || $project['name'] == $Name) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['projectExists']));
    },$Data['raweditor']['projects']);

    if(!is_writable($Path)) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['wrongPath']));

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

$_del = function() use($Json){
    $Name = isset($_POST['name']) ? $_POST['name'] : null;

    if(!$Name) die('Fuck off!!!');

    $Data = $Json->Data;
    $Projects = $Data['raweditor']['projects'];

    $NewProjects = array_filter($Projects,function($project) use($Name){
        return $project['name'] !== $Name;
    });

    if(count($Projects) == count($NewProjects)) throw new \Exception(strFupper(\Cmatrix\Local::get()->Data['projectNotExists']));

    $Data['raweditor']['projects'] = $NewProjects;
    $Json->setData($Data);
    $Json->put(CM_ROOT.'/config.json');

    return [
        'name' => $Name
    ];
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
        'message' => '',
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