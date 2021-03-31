<?php
require_once '../defs.php';
require_once '../common.php';

\Cmatrix\Router::add('/',[
    'template' => 'main',
    'model' => 'main'
]);

\Cmatrix\Router::add('/^project\/.*/',[
    'template' => 'project',
    'model' => 'project'
]);

echo \Cmatrix\App::get()->Html;
?>