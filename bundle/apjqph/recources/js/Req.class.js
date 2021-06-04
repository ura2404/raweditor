export default class Req {
    
    // --- --- --- --- ---
    constructor(data){
        this.Data = data;
    }
    
    readBinary(bit){
        console.log('-----------------------------------------------');
        console.log('data',this.Data);
        console.log('length',this.Data.length);
        
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
        
        
        /*
        bit = bit || 8;
        
        let Buff;
        if(bit == 32) Buff = new Int32Array(Len);
        if(bit == 16) Buff = new Int16Array(Len);
        else if(bit == 8) Buff = new Int8Array(Len);
        */
    }
    
    // --- --- ---- --- ---
    ajax(params,_success,_error){
        $.ajax(params).done(
            data => _success(data)
        ).fail(
            (data, textStatus, jqXHR) => _error(data)
        );
    }
    
    // --- --- ---- --- ---
    ajaxBinary(bit,params,_success,_error){
        bit = bit || 8;
        
        let Params = Object.assign({},{
            method : 'post',
            async : true,
            contentType: 'application/octet-stream',
            processData: false
        },params,{
            data : this.dataBinary(bit,this.Data)
        });
        
        //console.log(Params);
        
        this.ajax(Params,_success,_error);
    }
    
    // --- --- ---- --- ---
    dataBinary(bit,data){
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
        console.log('send buf',Buff);
        return Buff;
    }
    
}
