<?php
namespace Cmatrix;

class App {
    static $PAGE;
    static $PARAMS;

    private $Twig;

    // --- --- --- --- ---
    function __construct($pageName=null){
        self::$PAGE = $pageName ? $pageName : $this->probePage();

//dump($_GET);
//dump($_POST);
//dump($this->PageName);

        $this->Twig = new \Twig_Environment(new \Twig_Loader_Filesystem(CM_ROOT.'/templates/'), [
            'cache' => '/var/tmp',
            'debug' => true,
            'auto_reload' => true
        ]);
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Html' : return $this->getMyHtml();
        }
    }

    // --- --- --- --- ---
    private function probePage(){
        if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 200){
            $Page = strAfter(trim(rtrim($_SERVER['REDIRECT_QUERY_STRING'],'/')),'cmp=');
        }
        else{
            $Page = trim($_SERVER['REQUEST_URI'],'/');
        }

        $Page = $Page == '' ? '/' : $Page;
        return $Page;
    }

    // --- --- --- --- ---
    private function getMyHtml(){
        try{
            $_render = function($router){
                if(!$router) throw new \Exception('router is not defined');

                $Template = $router['template'].'.twig';
                $Model = isset($router['model']) ? $router['model'] : [];
                if($Model instanceof \Closure) $Data = $Model();
                else{
                    $ClassName = "\\Cmatrix\\Models\\".ucfirst($Model);
                    $Data = (new $ClassName())->getData();
                }

                return $this->Twig->render($Template,$Data);
            };

            $_simple = function($router){
                //dump($router,'simple');
                if(self::$PAGE !== $router['match']) return;
                return $router;
            };

            $_match =  function($router){
                //dump($router,'match');
                if(!preg_match($router['match'],self::$PAGE)) return;
                return $router;
            };

            foreach(\Cmatrix\Router::$ROUTERS as $router){
                $Match = $router['match'];
                if(strlen($Match)>2 && $Match{0} == '/' && $Match{strlen($Match)-1} == '/') $Router = $_match($router);
                else $Router = $_simple($router);

                if($Router) break;
            }

            if($Router) return $_render($Router);
            else if(isset(\Cmatrix\Router::$ROUTERS['404'])) $_render(\Cmatrix\Router::$ROUTERS['404']);
            else die(self::$PAGE.' is not exists');
        }
        //catch(\Exception $e)
        catch(\Throwable $e){
            dump($e->getMessage());
            dump($e->getTrace());
        }
    }

    // --- --- --- --- ---
    static function get($pageName=null){
        return new self($pageName);
    }
}
?>