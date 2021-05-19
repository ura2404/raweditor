<?php
define('CM_TOP',realpath(dirname(__FILE__).'/../..'));
define('CM_ROOT',realpath(dirname(__FILE__)));
define('CM_WHOME',$_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['SERVER_NAME'] .':'. $_SERVER['SERVER_PORT']. $_SERVER['REQUEST_URI']);
?>