<?php
namespace Cmatrix;

class Exception extends \Exception{
    private $Data;

    // --- --- --- --- ---
    function __construct($message=''){
        parent::__construct($message);
    }
}
?>