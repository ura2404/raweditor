<?php
header("Content-type: application/json");
require_once('../defs.php');
require_once('../common.php');

$Mode = isset($_POST['m']) ? $_POST['m'] : null;
if(!$Mode) die('Fuck off!!!');

// --- --- --- --- ---
$_node = function(){
    $Project = isset($_POST['project']) ? $_POST['project'] : null;
    $Hid = isset($_POST['hid']) ? $_POST['hid'] : null;
    if(!$Project || !$Hid) die('Fuck off!!!');
    
    $Cache = \Cmatrix\Cache::session()->folder('tree-'.$Project);
    $Json = $Cache->getJson($Hid);
    $Level = $Json['level'];
    
    $Path = \Cmatrix\Project::get($Project)->Path .'/'. $Json['parent'] .'/'. $Json['name'];

    $Dir = \Cmatrix\Dir::get($Path);
    $List = $Dir->getList(function(&$item) use($Cache,$Json){
        $item['parent'] = $Json['parent'] .'/'. $Json['name'];
        
        $item['hid'] = hid($item['parent'].'/'.$item['name']);
        $item['level'] = $Json['level'] + 1;
        
        $Cache->putJson($item['hid'],[
            'name' => $item['name'],
            'parent' => $item['parent'],
            'level' => $item['level']
        ]);
        
    });

    return [
        'message' => 'OK',
        'data' => [
            'list' => $List
        ]
    ];
};

// --- --- --- --- ---
$_file = function(){
    $Project = isset($_POST['project']) ? $_POST['project'] : null;
    $Hid = isset($_POST['hid']) ? $_POST['hid'] : null;
    if(!$Project || !$Hid) die('Fuck off!!!');
    
    $Cache = \Cmatrix\Cache::session()->folder('tree-'.$Project);
    $Json = $Cache->getJson($Hid);
    
    $Path = \Cmatrix\Project::get($Project)->Path .'/'. $Json['parent'] .'/'. $Json['name'];
    
    if(mime_content_type($Path) === 'application/octet-stream') throw new \Cmatrix\Exception(\Cmatrix\Local::getVal('file/binary'));
    
    $Json['content'] = file_get_contents($Path);

    return [
        'message' => 'OK',
        'data' => $Json
    ];
};

// --- --- --- --- ---
$_save = function(){
    $Project = isset($_POST['project']) ? $_POST['project'] : null;
    $Hid = isset($_POST['hid']) ? $_POST['hid'] : null;
    if(!$Project || !$Hid) die('Fuck off!!!');
    
    $Cache = \Cmatrix\Cache::session()->folder('tree-'.$Project);
    $Json = $Cache->getJson($Hid);
    
    $Path = \Cmatrix\Project::get($Project)->Path .'/'. $Json['parent'] .'/'. $Json['name'];
    
    if(!is_writable($Path)) throw new \Cmatrix\Exception(\Cmatrix\Local::getVal('file/notwritable'));
    
    $Content = isset($_POST['content']) ? $_POST['content'] : false;
    if($Content === false) throw new \Cmatrix\Exception(\Cmatrix\Local::getVal('file/emptywrite'));
    
    \Cmatrix\Vendor::reg('LZW');
    $LZW = new \LZW();
    
    dump($Content);
    $Content = $LZW->decompress($Content);
    dump($Content);
    
    //file_put_contents($Path,$Content);
    
    return [
        'message' => 'Файл успешно сохранён.'
    ];
};

// --- --- --- --- ---
// --- --- --- --- ---
// --- --- --- --- ---
try{
    switch($Mode){
        case 'node' : $Ret = $_node();break;
        case 'file' : $Ret = $_file();break;
        case 'save' : $Ret = $_save();break;
        default : die('Fuck off!!!');
    }

    echo json_encode(array_merge([
        'status' => 1
    ],$Ret));
}
catch(\Throwable $e){
//catch(\Exception $e){
    echo json_encode([
        'status' => -1,
        'message' => $e->getMessage()
    ]);
}
?>