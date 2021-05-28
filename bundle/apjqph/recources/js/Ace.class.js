export default class Ace {
    constructor($ace,params){
        params = params || {};
        
        this.Tag = $ace;
        this.Editor = ace.edit(this.Tag.attr('id'));
        this.Ide = params.ide;
        this.save = params.save;
        this.init();
        
        $ace.data('cm-ace',this);
    }
    
    init(){
        const Instance = this;
        
        this.Editor.setTheme("ace/theme/textmate");
        this.Editor.session.setMode("ace/mode/"+this.type());
        
        this.Editor.setOptions({
            enableBasicAutocompletion : true,       // автодополнение
            enableSnippets : true,                  // сниппеты
            enableEmmet : true,                     // zen coding
            
            scrollPastEnd : true,                   // прокрутка далее конца файла
        });
        
        this.Editor.commands.addCommand({
            name : "Save Ctrl+S",
            bindKey : { win : "Ctrl-S", mac : "Command-S" },
            exec : function(editor){
                Instance.save.call(Instance.Ide,Instance.Editor.getValue());
                //instance.save();
                //instance.onSave.call(this);
                //$(instance.Editor).removeData('changed');
            }
        });
        this.Editor.focus();
    }
    
    type(){
        let Ext = 'txt';
        const Name = this.Tag.data('cm-name');
        
        const Trans = {
            'txt' : 'text',
            'js'  : 'javascript',
            'md'  : 'markdown',
            'py'  : 'python'
        };
        
        if(Name.indexOf('.') === -1 || Name.indexOf('.') === 0) {}     // точка в начале или точки нет
        else Ext = Name.split('.').pop();
        
        return Trans[Ext] || Ext;
    }
}