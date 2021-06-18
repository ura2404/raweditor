<?php
namespace Cmatrix;

class Binary {
    private $Value;
    private $Bit;
    
    // --- --- --- --- ---
    function __construct($val,$bit=16){
        if($val > pow(2,$bit)-1) throw new \Exception('The value is greater than the permissible value.');
        
        $this->Value = $val;
        $this->Bit = $bit;
    }

    // --- --- --- --- ---
    function __get($name){
        if($name === 'Value') return $this->Value;
    }
    
    // --- --- --- --- ---
    /**
     * sprintf( "%016d",decbin($v) ) - вывод бинарного 16 битного числа
     */
    public function ror($n=1,$bit=null){
        $Bit = $bit ? $bit : $this->Bit;
        
        for($i=0; $i<$n; $i++){
            $b = $this->Value & 0x00000001;
            $this->Value >>= 1;
            $this->Value |= $b<<($Bit-1);
        }
        return $this->Value;
    }
    
    // --- --- --- --- ---
    public function rol($n=1,$bit=null){
        $Bit = $bit ? $bit : $this->Bit;
        //dump($Bit);
        
        for($i=0; $i<$n; $i++){
            $b = $this->Value & pow(2,$Bit-1) ? 1 : 0;
            $this->Value <<= 1;
            $this->Value = $this->Value >= pow(2,$Bit) ? $this->Value - pow(2,$Bit) : $this->Value;
            $this->Value |= $b;
        }
        return $this->Value;
    }

    // --- --- --- --- ---
    public function pp($bit=null){
        $Bit = $bit ? $bit : $this->Bit;
        return sprintf( "%0".$Bit."s",decbin($this->Value) );
    }
    
    // --- --- --- --- ---
    static function get($val,$bit=16){
        return new self($val,$bit);
    }
}
?>