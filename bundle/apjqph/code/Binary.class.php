<?php
namespace Cmatrix;

class Binary {
    // --- --- --- --- ---
    /**
     * sprintf( "%016d",decbin($v) ) - вывод бинарного 16 битного числа
     */
    static function ror($val,$bit=16){
        $b = $val & 0x00000001;
        $val >>= 1;
        $val |= $b<<($bit-1);
        return $val;
    }
    
    // --- --- --- --- ---
    static function rol($val,$bit=16){
        //$b = $val & 0x8000 ? 1 : 0;
        $b = $val & pow(2,$bit-1) ? 1 : 0;
        $val <<= 1;
        $val = $val > pow(2,$bit) ? $val - pow(2,$bit) : $val;
        $val |= $b;
        return $val;
    }
    
/*  
  unsigned int left_shift(unsigned int n,unsigned int k) {
 unsigned int i,bit;
 for (i=0; i<k; i++) {
  bit=n&0x8000?1:0;
  n<<=1;
  n|=bit;
 }
 return n;
}*/

    // --- --- --- --- ---
    function pp($val,$bit=16){
        return sprintf( "%0".$bit."s",decbin($val) );
    }

}
?>