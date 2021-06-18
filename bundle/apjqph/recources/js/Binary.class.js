export default class Binary {
    
    // --- --- ---- --- ---
    constructor(value,bit=16){
        this.Value = value;
        this.Bit = bit;
        
        if(value > Math.pow(2,bit)-1) throw new Error('The value is greater than the permissible value.');
    }
    
    // --- --- ---- --- ---
    ror(n=1,bit=null){
        bit = bit || this.Bit;
        
        for(let i=0; i<n; i++){
            let b = this.Value & 0x00000001;
            this.Value >>= 1;
            this.Value |= b<<(bit-1);
        }
        
        return this.Value;
    }
    
    // --- --- ---- --- ---
    rol(n=1,bit=null){
        bit = bit || this.Bit;
        
        for(let i=0; i<n; i++){
            let b = this.Value & Math.pow(2,bit-1) ? 1 : 0;
            this.Value <<= 1;
            this.Value = this.Value >= Math.pow(2,bit) ? this.Value - Math.pow(2,bit) : this.Value;
            this.Value |= b;
        }
        
        return this.Value;
    }
    
    // --- --- ---- --- ---
    /** 
     * вариант 1
     *  //return (this.Value >>> 0).toString(2);
     * 
     * вариант 2
     *  if(this.Value >= 0) {
     *      return this.Value.toString(2);
     *  }
     *  else {
     *       // Here you could represent the number in 2s compliment but this is not what 
     *       // JS uses as its not sure how many bits are in your number range. There are 
     *       //some suggestions https://stackoverflow.com/questions/10936600/javascript-decimal-to-binary-64-bit 
     *       return (~this.Value).toString(2);
     *  }
    */
    pp(bit=null){
        bit = bit || this.Bit;
        
        let Value = this.Value >= 0 ? this.Value.toString(2) : (~this.Value).toString(2);
        
        let Arr = Value.toString(2).split('').reverse();
        return '0'.repeat(bit).split('').reverse().map(function(current,index,array){
            return Arr[index] | 0;
        }).reverse().join('');
    }
    
}