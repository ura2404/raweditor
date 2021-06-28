import Binary from './Binary.class.js';

export default class Req {
    
    // --- --- --- --- ---
    /**
     * @param string data
     */
    constructor(data){
        this.Data = data;
    }
    
    // --- --- ---- --- ---
    ajax(params,_success,_error){
        console.log('ajax',params);
        
        $.ajax(params)
        .done(
            data => _success(data)
        )
        .fail(
            (data, textStatus, jqXHR) => _error(data)
        );
    }
    
    // --- --- ---- --- ---
    ajaxJson(params,_success,_error){
        let Params = Object.assign({},{
            method : 'post',
            async : true,
            dataType : 'json',
        },params,{
            data : this.Data
        });
        
        this.ajax(Params,_success,_error);
    }
    
    // --- --- ---- --- ---
    ajaxBinary(params,_success,_error){
        let Params = Object.assign({},{
            method : 'post',
            async : true,
            contentType: 'application/octet-stream',
            processData: false
        },params,{
            //data : this.encode()
            //data : this.Data
            //data : JSON.stringify(this.Data)
            data : this.binEncode()
        });
        
        this.ajax(Params,_success,_error);
    }
    
    // --- --- ---- --- ---
    /**
     * @param string data
     */
    binEncode(){
        console.log('before encode->',this.Data);
        
        let Buff = new Uint16Array(this.Data.length);
        
        for (let i = 0; i < this.Data.length; i++){
            Buff[i] = new Binary(this.Data.charCodeAt(i)).ror();
            //Buff[i] = new Binary(this.Data.charCodeAt(i)).Value;
        }
        console.log('after encode->',Buff);
        return Buff;
    }    
    
    // --- --- ---- --- ---
    /**
     * @return string
     */
    binDecode(){
        console.log('before decode',this.Data,this.Data.length);
        
        let Data = '', Buff = new Uint16Array(this.Data.length / 2);
        
        for (let k=0,i = 0; i < this.Data.length; i += 2){
            //let D1 = new Binary(this.Data.charCodeAt(i)).ror() | (this.Data[i+1] != '\u0000' ? new Binary(this.Data.charCodeAt(i+1)<<8).ror() : new Binary(this.Data.charCodeAt(i+1)<<8).Value );
            
            Buff[k] = new Binary(this.Data.charCodeAt(i)).Value | new Binary(this.Data.charCodeAt(i+1)<<8).Value ;
            Buff[k] = new Binary(D).rol();
            
            /*
            //let D = this.Data.charCodeAt(i);// | (this.Data[i+1] != '\u0000' ? this.Data.charCodeAt(i+1)<<8 : 0);
            let D = this.Data[i] | this.Data[i+1]<<8;
            D = String.fromCharCode(D);
            let D1 = new Binary(D).ror();
            */
            //Buff[k] = D1;
            Data += String.fromCharCode(Buff[k]);
            console.log(this.Data[i+1] != '\u0000',i,this.Data[i],this.Data[i+1],D,D1,String.fromCharCode(D1));
            
            k++;
        }
        console.log('after decode',Buff);
        console.log('after decode',Data);
        return Data;
    }
    
/*
    // --- --- ---- --- ---
    encode(){
        let Data = JSON.stringify(this.Data);
        
        for(var result = '', int32, i = 0; i < Data.length;){
            int32 = Data.charCodeAt(i++) << 16 | Data.charCodeAt(i++);
            int32 = int32 << 2 | int32 >>> 30;
            result += String.fromCharCode( int32 >>> 16, int32 & 65535 );
        }
        return result;
    }

    // --- --- ---- --- ---
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
}
