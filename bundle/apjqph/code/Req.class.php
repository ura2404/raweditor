<?php
namespace Cmatrix;

/**
 * Структура пакета данных
 *     -тип 1-int, 2-float, 3-string
 *     -длина
 *     -тело
 * 
 * @param string $format - формат (https://www.php.net/manual/ru/function.pack.php)
 *     - C* - набор беззнаковых символов (char)
 *     - S* - набор беззнаковых short (всегда 16 бит, машинный байтовый порядок)
 */
class Req {
    private $Data;

    // --- --- --- --- ---
    function __construct($data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function binEncode($format='C*'){
        $Data = '';
        $Index = 0;
        
        for($i=0; $i<count($this->Data); $i++){
            $Type = gettype($this->Data[$i]);
            
            if($Type === 'integer'){
                $Data .= pack($format,1);
                $Data .= pack($format,1);
                $Data .= pack($format,$this->Data[$i]);
            }
            elseif($Type === 'string'){
                $Data .= pack($format,3);
                $Data .= pack($format,strlen($this->Data[$i]));
                for($j=0; $j<strlen($this->Data[$i]); $j++) $Data .= pack($format,mb_ord($this->Data[$i][$j]));
            }
        }
        return $Data;
    }
    
    // --- --- --- --- ---
    public function binDecode($format='C*'){
        $Arr = [];
        $Data = unpack($format,$this->Data);
        $Count = count($Data);
        
        
        $_rec = function($index=1) use($Data,$Count,&$Arr,&$_rec){
            $Type = $Data[$index++];
            $Len = $Data[$index++];
            
            $Buff = '';
            if($Type == 1) for($i=0; $i<$Len; $i++) $Buff .= $Data[$index++];
            elseif($Type == 2){}
            elseif($Type == 3) for($i=0; $i<$Len; $i++) $Buff .= mb_chr($Data[$index++]);
            
            $Arr[] = $Buff;
            
            if($index < $Count) $_rec($index);
        };
        
        if($Count) $_rec();
        return $Arr;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function get($data){
        return new self($data);
    }
    
    // --- --- --- --- ---
    /*static function arr($key=null,$def=null){
        $Arr = self::get()->Array;
        return $key ? (isset($Arr[$key]) ? $Arr[$key] : $def) : $Arr;
    }*/

    // --- --- --- --- ---
    static function readBinary($format='C*'){
        $Data = file_get_contents('php://input');
        return self::get($Data)->binDecode($format);
    }
    
}
?>