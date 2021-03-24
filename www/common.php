<?php
define('CM_ROOT',realpath(dirname(__FILE__).'/..'));

require_once 'Twig/autoload.php';
require_once CM_ROOT.'/code/utils.php';

spl_autoload_register(function($className){
    if(class_exists($className)) return;

    $Arr = explode("\\",$className);
    if($Arr[0] !== 'Cmatrix') return;

    $Path = CM_ROOT .'/code/'. $Arr[1] .'.class.php';
    if(file_exists($Path)){
        require_once($Path);
    }
    else throw new \Exception('Class "'. $className .'" file not found.');

/*
    
    $Arr = explode("\\",$className);
    if(count($Arr)< 3) return;
    
    $Module = array_shift($Arr);
    if($Module !== 'Cmatrix') return;
    
    $Part = array_shift($Arr);
    
    if($Arr[0] === 'Dm'){
        array_shift($Arr);
        $Dst = 'dm';
    }
    elseif($Arr[0] === 'Ds'){
        array_shift($Arr);
        $Dst = 'ds';
    }
    else{
        $Dst = 'code';
    }
    
    $ClassName = implode("\\",$Arr);
    $ClassPath = dirname(__FILE__) .CM_DS. $Part .CM_DS. $Dst .CM_DS. str_replace("\\",CM_DS,$ClassName) .'.class.php';
    
    //dump($ClassPath);
    
    if(file_exists($ClassPath)){
        require_once($ClassPath);
    }*/
},true,true);


//define('RAW_ROOT',realpath(dirname(__FILE__).'/..'));
?>