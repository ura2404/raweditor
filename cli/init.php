#!/usr/bin/php
<?php
/**
 * Настроечный скрипт
 */
$Root = realpath(dirname(__FILE__) .'/..');
require_once $Root.'/code/utils.php';

$_config = function() use($Root){
    $File = $Root.'/config.json';

    $_create = function() use($Root){
        $Arr =[
            'apache2' => [
                'rewrite' => '/raweditor'
            ],
            'raweditor' => [
                'lang' => 'ru',
                'projects' => [
                    /*'raweditor' => [
                        'name' => 'Raw Editor',
                        'path' => $Root
                    ]*/
                ]
            ]
        ];

        return $Arr;
    };

    $_update = function() use($File){
        $Arr = json_decode(file_get_contents($File),true);
        return $Arr;
    };

    if(!file_exists($File)) $Json = $_create();
    else $Json = $_update();

    file_put_contents($File, json_encode($Json,
        JSON_PRETTY_PRINT             // форматирование пробелами
        | JSON_UNESCAPED_SLASHES      // не экранировать /
        | JSON_UNESCAPED_UNICODE      // не кодировать текст
    ));
};

$_htpasswd = function() use($Root){
    $Htaccess = $Root . '/www/.htaccess';
    $Htpasswd = $Root . '/www/.htpasswd';

    $File = file_get_contents($Htaccess);
    $Arr = explode('AuthUserFile ', $File);
    $Arr2 = explode(PHP_EOL,$Arr[1]);

    $Arr2[0] = $Htpasswd;
    $Arr[1] = implode(PHP_EOL,$Arr2);
    $File = implode('AuthUserFile ', $Arr);

    file_put_contents($Htaccess, $File);
};

$_rewrite = function() use($Root){
    $_base = function() use($Root){
        $File = $Root.'/config.json';
        $Arr = json_decode(file_get_contents($File),true);
        return $Arr['apache2']['rewrite'];
    };

    $Htaccess = $Root . '/www/.htaccess';
    $File = file_get_contents($Htaccess);
    $Arr = explode('RewriteBase ', $File);
    $Arr2 = explode(PHP_EOL,$Arr[1]);

    $Arr2[0] = $_base();
    $Arr[1] = implode(PHP_EOL,$Arr2);
    $File = implode('RewriteBase ', $Arr);

    file_put_contents($Htaccess, $File);
};

$_config();
$_htpasswd();
$_rewrite();
?>