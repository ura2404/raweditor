<?php
require_once 'Twig/autoload.php';
require_once CM_ROOT.'/code/utils.php';

spl_autoload_register(function($className){
    if(class_exists($className)) return;

    $Arr = explode("\\",$className);
    if($Arr[0] !== 'Cmatrix') return;

    if($Arr[1] === 'Models') $Path = CM_ROOT .'/models/'. $Arr[2] .'.model.php';
    else $Path = CM_ROOT .'/code/'. $Arr[1] .'.class.php';

    if(file_exists($Path)) require_once($Path);
    else throw new \Exception('Class "'. $className .'" file not found.');

},true,true);
?>