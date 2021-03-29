<?php
require_once '../defs.php';
require_once '../common.php';

\Cmatrix\Router::add('/',[
    'template' => 'main',
    'model' => 'main'
]);

echo \Cmatrix\App::get()->Html;
/*
return;

require_once 'controller.php';
require_once 'model.php';
require_once 'view.php';

$Project = isset($_GET['project']) ? $_GET['project'] : null;

if($Project){
    $controller = Controller::get(View::get(),Model::get());
    $html = $controller->Html;
    echo $html;
}

$controller = Controller::get(View::get(),Model::get());
$html = $controller->Html;
echo $html;
*/
?>