import Binary from './Binary.class.js';

export default class Req {
    
    // --- --- --- --- ---
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
        
        let Data;
        if(typeof this.Data === 'object') Data = JSON.stringify(this.Data);
        else Data = this.Data;
        console.log('before encode->',Data);
        
        let Buff = new Uint16Array(Data.length);
        
        for (let i = 0; i < Data.length; i++){
            //Buff[i] = Data.charCodeAt(i);
            //Buff[i] = new Binary(Data.charCodeAt(i)).ror();
            Buff[i] = new Binary(Data.charCodeAt(i)).Value;
        }
        console.log('after encode->',Buff);
        return Buff;
    }    
    
    // --- --- ---- --- ---
    binDecode(){
        console.log('before decode',this.Data,this.Data.length);
        
        let Buff = new Uint16Array(this.Data.length / 2);
        
        for (let k=0,i = 0; i < this.Data.length; i += 2){
            
            let D = (this.Data[i] << 8) | (this.Data[i+1]);
            Buff[k++] = this.Data.charCodeAt(i) | this.Data.charCodeAt(i+1);
            
            console.log(i,typeof this.Data[i],this.Data[i],this.Data[i+1],this.Data[i] << 8,Buff[k-1]);
        }
        
        
        
        console.log('after decode',Buff);
        /*
            var ints = [];
            for (var i = 0; i < byteArray.length; i += 2) {
                console.log('i',byteArray[i],byteArray[i+1]);
                // Note: assuming Big-Endian here
                ints.push((byteArray[i] << 8) | (byteArray[i+1]));
            }
            return ints;
        */
        
    }
    
    /*
    // --- --- ---- --- ---
    binEncode(bit,data){
        
        bit = bit || 8;
        
        let Len = 0;
        for (let i = 0; i < data.length; i++){
            if(typeof data[i] === 'number') Len += 3;
            else if(typeof data[i] === 'string') Len = Len + 2 + data[i].length;
        }
        
        //console.log(Len);
        
        let Buff;
        if(bit == 32) Buff = new Int32Array(Len);
        if(bit == 16) Buff = new Int16Array(Len);
        else if(bit == 8) Buff = new Int8Array(Len);
        
        let Index = 0;
        for (let i = 0; i < data.length; i++){
            if(typeof data[i] === 'number'){
                Buff[Index++] = 1;
                Buff[Index++] = 1;
                Buff[Index++] = data[i];
            }
            else if(typeof data[i] === 'string'){
                Buff[Index++] = 3;
                Buff[Index++] = data[i].length;
                for (let j = 0; j < data[i].length; j++) Buff[Index++] = data[i].charCodeAt(j);
            }
        }
        console.log('send data',data);
        console.log('send buf',Buff);
        return Buff;
    }
    
    binDecode(bit){
        console.log('-----------------------------------------------');
        console.log('bit',bit);
        console.log('data',this.Data);
        console.log('length',this.Data.length);
        
        let Buff2 = new Int8Array(this.Data.length);
        
        for (var i = 0; i < this.Data.length; i++){
            Buff2[i] = String.fromCharCode(this.Data[i]);
            console.log(this.Data[i],this.Data[i]==0x0001);
        }
        
        console.log('buff',Buff2);
        return;
        
        //let Buff = new Int16Array(this.Data.length);
        //for (let i = 0; i < this.Data.length; i++){
        //    Buff[i] = this.Data[i];
        //}
        let Buff = new Int16Array(this.Data);
        
        function arrayByteToInt16(byteArray) {
            var ints = [];
            for (var i = 0; i < byteArray.length; i += 2) {
                console.log('i',byteArray[i],byteArray[i+1]);
                // Note: assuming Big-Endian here
                ints.push((byteArray[i] << 8) | (byteArray[i+1]));
            }
            return ints;
        }
        
        
        console.log('buff',Buff);
        console.log('buff',arrayByteToInt16(this.Data));
        
        //bit = bit || 8;
        
        //let Buff;
        //if(bit == 32) Buff = new Int32Array(Len);
        //if(bit == 16) Buff = new Int16Array(Len);
        //else if(bit == 8) Buff = new Int8Array(Len);
        
    }
    */
    
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
}
