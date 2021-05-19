<?php
define('CM_TOP',realpath(dirname(__FILE__).'/../..'));
define('CM_ROOT',realpath(dirname(__FILE__)));
//define('CM_WHOME',$_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['SERVER_NAME'] .':'. $_SERVER['SERVER_PORT']. $_SERVER['REQUEST_URI']);

$Config = json_decode(file_get_contents(CM_TOP.'/config.json'),true);
if(!isset($Config['www']) || !isset($Config['www']['root'])) die('Wrong config file!');
define('CM_WHOME',$_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['SERVER_NAME'] .':'. $_SERVER['SERVER_PORT']. $Config['www']['root']);
?>