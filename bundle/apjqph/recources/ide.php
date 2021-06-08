<?php
//header("Content-type: application/json");
header("Content-type: application/octet-stream");
require_once('../defs.php');
require_once('../common.php');

$I = 43690;
//dump($I);
dump(decbin($I));

//$Y = $I<<32;
$Y = ($I << 29) + ($I >> 3);
//dump($Y);
dump(decbin($Y));

$Z = $I>>2;
//dump($Z);
dump(decbin($Z));

dump('-------------------------');

$k=bindec('11111100110011001100110011000000');
echo sprintf('%032b',$k)."\n";
for($a=1;$a<=32;$a++){
    $b=32-$a;
    $s = (($k >> $a) & ~(-pow(2,$b)))^($k << $b);
    echo sprintf('%032b',$s)."\n";
}


die();

try{
    $Arr = \Cmatrix\Req::readEncode();
    dump($Arr,'AAAAAAAAAAAAA');
    
    //echo \Cmatrix\Req::get([1,'OK'])->binEncode('S*');
    //echo \Cmatrix\Req::get([1,'OK'])->binEncode();
}
catch(\Throwable2 $e){
    dump($e->getMessage());
    //echo \Cmatrix\Req::get([-1,$e->getMessage()])->binEncode('S*');
}

return;

$Data = file_get_contents('php://input');

dump(gettype($Data),'type');
dump(strlen($Data),'len');
dump($Data);
dump('--------------------');

$Arr = unpack('S*',$Data);
dump($Arr);

// --- --- ---
$D = 0;
$Mode = $Arr[$D+1];

// --- --- ---
$D = 1;
$Hid = '';
for($i=0; $i<32; $i++) $Hid .= chr($Arr[$i+$D+1]);

// --- --- ---
$D = 1 + 32;
$ProjectLength = $Arr[$D+1];

// --- --- ---
$D = 1 + 32 + 1;
$Project = '';
for($i=0; $i<$ProjectLength; $i++) $Project .= mb_chr($Arr[$i+$D+1]);

// --- --- ---
$D = 1 + 32 + 1 + $ProjectLength;
$Data = '';
for($i=0; $i<count($Arr)-$D; $i++) $Data .= mb_chr($Arr[$i+$D+1]);

dump($Mode,'Mode');
dump($Hid,'Hid');
dump($ProjectLength,'ProjectLength');
dump($Project,'Project');

dump($Data);

switch($Mode){
    // get file
    case 1 : 
        
}









return;
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
    
    $Content = isset($_POST['content']) ? $_POST['content'] : false;
    dump(strlen($Content));
    dump($Content);
    return;
    
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