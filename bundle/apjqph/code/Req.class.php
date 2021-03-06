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
    /**
     * @param string $data
     */
    function __construct($data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Array' : parse_str($this->Data,$Data);
                           return $Data;
                            
            case 'Data'      : return $this->getData();
            case 'BinDecode' : return $this->binDecode();
            case 'BinEncode' : return $this->binEncode();
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return string
     */
    public function binDecode(){
        $Data = unpack('S*',$this->Data);
        
        //dump($this->Data,'before decode');
        //dump($Data,'before decode');
        
        $Buff = '';
        for($i=1; $i<=count($Data); $i++){
            $Buff .= mb_chr(Binary::get($Data[$i])->rol());
            //$Buff .= mb_chr(Binary::get($Data[$i])->Value);
        }
        
        //dump($Buff,'after decode');
        
        return $Buff;
    }
    
    // --- --- --- --- ---
    /**
     * @return string
     */
    public function binEncode(){
        $Data = '';
        if(is_array($this->Data)) $Data = json_encode($this->Data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        else $Data = $this->Data;
        
        //dump($this->Data,'before encoding');
        //dump($Data,'before encoding');
        
        $Buff = [];
        for($i=0; $i<mb_strlen($Data); $i++){
            //$Buff[] = mb_ord(mb_substr($Data,$i,1));
            //$Buff[] = pack('S',mb_ord(mb_substr($Data,$i,1)));
            
            //dump(mb_ord(mb_substr($Data,$i,1)));
            //dump(Binary::get(mb_ord(mb_substr($Data,$i,1)))->pp());
            
            $Buff[] = pack('S',
                Binary::get(
                    mb_ord(mb_substr($Data,$i,1))
                )
                //->Value
                ->ror()
            );
            //$Buff[] = Binary::get( pack('S', mb_ord(mb_substr($Data,$i,1)) ) )->rol();
        }
        
        //dump($Buff);
        
        //return $Buff;
        return implode('',$Buff);
    }    
    
    /*
    public function binEncode($format='C*'){
        $Data = '';
        $Index = 0;
        
        for($i=0; $i<count($this->Data); $i++){
            $Curr = $this->Data[$i];
            $Type = gettype($Curr);
            
            if($Type === 'integer'){
                $Data .= pack($format,1);
                $Data .= pack($format,1);
                $Data .= pack($format,$Curr);
            }
            elseif($Type === 'string'){
                $Data .= pack($format,3);
                $Data .= pack($format,strlen($Curr));
                for($j=0; $j<strlen($Curr); $j++) $Data .= pack($format,mb_ord($Curr[$j]));
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
    public function decode(){
        dump($this->Data);
        
        $_charCodeAt = function($string, $offset){
            $string = mb_substr($string, $offset, 1);
            list(, $ret) = unpack('S', mb_convert_encoding($string, 'UTF-16LE'));
            return $ret;
        };
        
        $_fromCharCode = function(){
            $output = '';
            $chars = func_get_args();
                foreach($chars as $char){
                $output .= chr((int) $char);
            }
            return $output;
        };
        
        // The >>> javascript operator in php x86_64
        // Usage: -1149025787 >>> 0 ---> rrr(-1149025787, 0) === 3145941509
        // @param int $v
        // @param int $n
        // @return int
        $_rrr = function($v, $n){
            return ($v & 0xFFFFFFFF) >> ($n & 0x1F);
        };
        
        // The >> javascript operator in php x86_64
        // @param int $v
        // @param int $n
        // @return int
        $_rr = function($v, $n){
            return ($v & 0x80000000 ? $v | 0xFFFFFFFF00000000 : $v & 0xFFFFFFFF) >> ($n & 0x1F);
        };
        
        // The << javascript operator in php x86_64
        // @param int $v
        // @param int $n
        // @return int
        $_ll = function($v, $n){
            return ($t = ($v & 0xFFFFFFFF) << ($n & 0x1F)) & 0x80000000 ? $t | 0xFFFFFFFF00000000 : $t & 0xFFFFFFFF;
        };
        
        $source = '';
        for($i=0; $i<strlen($this->Data);){
            $int32 = $_ll($_charCodeAt($this->Data,$i++),16) | $_charCodeAt($this->Data,$i++);
            $int32 = $_rrr($int32,2) | $_ll($int32,30);
            $source += $_fromCharCode( $_rrr(int32,16), int32 & 65535 );
        }
        
        $Data = $_charCodeAt($source,i-1) === 0 ? substr(source,-1) : source;
        dump($Data);
    }
    */
    
/*
function charCodeAt($string, $offset) {
  $string = mb_substr($string, $offset, 1);
  list(, $ret) = unpack('S', mb_convert_encoding($string, 'UTF-16LE'));
  return $ret;
}

    decode(){
        for(var source = '', int32, i = 0; i < this.Data.length; ) {
            int32 = this.Data.charCodeAt(i++) << 16 | this.Data.charCodeAt(i++);
            int32 = int32 >>> 2 | int32 << 30;
            source += String.fromCharCode( int32 >>> 16, int32 & 65535 );
        }
        
        let Data = source.charCodeAt(i-1) === 0 ? source.slice(0, -1) : source;
        
        return JSON.parse(Data);
    }
*/    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function get(){
        $Data = file_get_contents('php://input');
        return new self($Data);
    }
    
    // --- --- --- --- ---
    /**
     * @param string $data
     */
    static function create($data){
        return new self($data);
    }


    // --- --- --- --- ---
    /*static function arr($key=null,$def=null){
        $Arr = self::get()->Array;
        return $key ? (isset($Arr[$key]) ? $Arr[$key] : $def) : $Arr;
    }

    // --- --- --- --- ---
    static function readEncode(){
        $Data = file_get_contents('php://input');
        dump($Data,'DDDDDDDDDDDDDD');
        
        //return self::get($Data)->decode();
    }
    */
}
?>