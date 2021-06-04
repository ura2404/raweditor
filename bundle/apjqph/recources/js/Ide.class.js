import Ace from './Ace.class.js';
import Message from './Message.class.js';
import Req from './Req.class.js';

export default class Ide {
    
    // --- --- --- --- ---
    constructor($tag){
        const Instance = this;
        
        this.$Ide = $tag;
        this.CurrentHid = null;
        
        this.Message = new Message();
        
        this.Project = this.$Ide.attr('data-project');
        this.$Direct = this.$Ide.find('.cm-ide-direct');
        this.$Tree = this.$Ide.find('.cm-ide-tree');
        this.$TreeContainer = this.$Tree.find('.cm-container');
        
        this.$Splitter = this.$Ide.find('.cm-splitter');
        
        this.$NodeTemplate = this.$Tree.find('.cm-template');
        
        this.$Current = $('#cm-current');
        this.$CurrentContainer = this.$Current.find('.cm-container');
        
        this.$List = $('#cm-list');
        this.$ListContainer = this.$List.find('.cm-container');
        this.$ListTemplate = this.$ListContainer.find('.cm-template');
        this.ListCount = 0;
        
        this.$Ace = $('.cm-ide-ace');
        this.$AceHeader = this.$Ace.find('.cm-header .cm-text');
        this.$AceContainer = this.$Ace.find('.cm-container');
        this.$AceTemplate = this.$AceContainer.find('.cm-template');
        
        this.$Actions = $('#cm-actions');
    }

    // --- --- --- --- ---
    init(){
        const Instance = this;
        
        this.$Direct.find('i.cm-action-tree').on('click',function(){
            $(this).toggleAttr('active');             // атрибут кнопки
            Instance.$Tree.toggleAttr('visible');     // атрибут дерева
            Instance.$Splitter.toggleAttr('visible'); // атрибут сплитера
        });
        
        this.$Tree
            .find('.cm-line').on('dblclick',function(e){
                e.preventDefault();
                const $Node = $(this).closest('li');
                if($Node.hasAttr('data-status')) Instance.treeNodeExpand($Node);
                else Instance.treeNodeOpen($Node);
            })
            .end()
            .find('.cm-line').on('click',function(){                    // click на строке дерева
                Instance.$Tree.find('.cm-line').removeAttr('active');
                $(this).toggleAttr('active');                           // атрибут кнопки
            })
            .end()
            .resizable({
                handleSelector: ".cm-splitter",
                resizeHeight: false
            });
            
        this.$ListTemplate
            .on('click',function(e){
                Instance.selectFile($(this).data('hid'));
            })
            .find('.cm-close').on('click',function(e){
                e.stopPropagation();
                if($(this).parent().hasClass('.cm-pushed')) return;
                Instance.closeFile($(this).parent().data('hid'));
            }).end()
            .find('.cm-push').on('click',function(e){
                e.stopPropagation();
                Instance.pushFile($(this).parent());
            });
            
        /**
         * открыть / закрыть список файлов
         */
        const _list = function(){
            Instance.$Current.toggleClass('cm-opend');
            Instance.$List.toggleClass('cm-opend');
        };
        
        this.$Current.find('.cm-left').on('click',() => Instance.nextFile(-1));
        this.$Current.find('.cm-right').on('click',() => Instance.nextFile(1));
        
        this.$List.on('click',_list);
        this.$CurrentContainer.on('click',_list);
        
        this.$Actions.find('.cm-save').on('click',() => Instance.saveFile());
    }

    // --- --- --- --- ---
    timer(val){
        return new Promise(function(resolve, reject){
            setTimeout(() => resolve(),val);
        });
    }

    // --- --- --- --- ---
    cursor(val=true){
        val ? $('body').addClass('waiting') :
        new Promise(function(resolve,reject){
            if($('body').hasClass('waiting')) resolve();
        }).then(() => $('body').removeClass('waiting'));
    }

    // --- --- --- --- ---
    /**
     * expand tree node
     */
    treeNodeExpand($node){
        const Instance = this;
        
        // установка атрибута data-status для li (узел папки)
        const _expand = function(){
            $node.attr('data-status',function(index, value){
                return value =='0' ? '1' : '0';
            });
        };
        
        const _addNode = function(data){
            let List = data.data.list;
            
            const $Tab = Instance.$NodeTemplate.find('.cm-tab');
            let $Container = $node.append('<ul></ul>').children('ul');
            
            for(let i=0; i<Object.keys(List).length; i++){
                const Name = List[i].name;
                const Hid = List[i].hid;
                
                Instance.$NodeTemplate.clone(true,true).removeClass('cm-template').data('hid',Hid).map(function(index, element){
                    $(this)/*.attr('data-hid',Hid)*/.children('div').attr('title',Name).children('.cm-text').text(Name);
                    
                    // отступ в количестве level-1, один отступ уже есть в шаблоне
                    for(let j=0; j<List[i].level-1; j++){
                        $Tab.clone(true,true).prependTo($(this).children('.cm-line'));
                    }
                    
                    // оставить нужную икону (папка или файл)
                    if(List[i].type === 'folder') $(this).find('.cm-file').remove();
                    else $(this).removeAttr('data-status').find('.cm-folder').remove();
                    
                }).end().appendTo($Container);
            }
        };
        
        const _success = function(data){
            _addNode(data);
            _expand();
            Instance.cursor(false);
        };
        
        const _error = function(data){
            Instance.Message.error(data.message);
            Instance.cursor(false);
        };
        
        // --- --- --- --- ---
        // если есть не пустрой ul (есть дочерние узлы) или data-status = 1 (папка открыта), то считается, что дочерние узлы прогружены и нужно только скрыть/раскрыть
        // иначе нужно подгрузить дочерние узлы
        if($node.find('ul').length || $node.attr('data-status') == '1') _expand();
        else {
            Instance.cursor();
            Instance.ajax({
                m : 'node',
                hid : $node.data('hid')
            },_success,_error);
        }
    }
    
    // --- --- --- --- ---
    /**
     * visible or not visible current and list
     * 
     * @param bool def - режим 
     *      если true, то просто открыть,
     *      иначе проверить, есть ли открытые фалы
     */
    checkCurrent(def=null){
        //console.log('list container count',this.$ListContainer.children('div:not([id])').length);
        
        this.ListCount = this.$ListContainer.children('div:not(.cm-template)').length;
        
        if(def === true || this.ListCount){
            this.$Current.addClass('cm-visible');
            this.$List.addClass('cm-visible');
            
            if(this.ListCount > 1) this.$Current.addClass('cm-directable');

        }
        else {
            this.$Current.removeClass('cm-visible');
            this.$List.removeClass('cm-visible');
        }
    }
    
    // --- --- --- --- ---
    /**
     * открыть файл из дерева
     */
    treeNodeOpen($node){
        const Instance = this;
        const Name = $node.find('.cm-text').text();
        const Hid = $node.data('hid');
        
        const _success = function(data){
            new Req(data).readBinary(16);
            
            return;
            
            
            const Parent = data.data.parent;
            const Content = data.data.content;
            
            new Promise(function(resolve, reject){
                const $Ace = Instance.$AceTemplate.clone(true,true)
                    .removeClass('cm-template')
                    .data('hid',Hid)
                    .attr('id',Hid)         // для плагина ace
                    .data('cm-name',Name)
                    .text(Content)
                    .appendTo(Instance.$AceContainer);
                
                const $Element = Instance.$ListTemplate.clone(true,true)
                    .removeClass('cm-template')
                    .data('hid',Hid)
                    .data('cm-name',Name)
                    .data('cm-parent',Parent)
                    .find('.cm-text')
                    .attr('title',Parent+'/'+Name).text(Name).end()
                    .appendTo(Instance.$ListContainer);
                    
                resolve([$Ace,$Element]);
            }).then(data => {
                Instance.CurrentHid = Hid;
                
                new Ace(data[0],{
                    ide : Instance,
                    save : Instance.saveFile
                });
                
                Instance.selectFile(data[1].data('hid'));
                Instance.checkCurrent(true);
                
                Instance.cursor(false);
            });
        };
        
        const _error = function(data){
            console.log('error',data);
            
            Instance.cursor(false);
            Instance.Message.error(data.message);
        };
        
        // --- --- --- --- ---
        // если файл открывался выбрать его
        // иначе получить параметры из кеша
        if(
            this.$ListContainer.find('.cm-element:not([id])').map(function(index, element){
                return $(this).data('hid') === Hid;
            }).get().every(function(val){ return !val; })
        ){
            // если файл НЕ открыт - получить параметры мз кеша и клонировать шаблон
            Instance.cursor();
            //Instance.ajax({
            //    m : 'file',
            //    hid : $node.data('hid')
            //},_success,_error);
            
            //Instance.ajax(1,Hid,'12345 qaz',_success,_error);
            
            new Req([1,Hid,'12345 qaz подкова']).ajaxBinary(16,{
                url : 'res/res/ide.php',
            },_success,_error);

            
        }
        else {
            // если файл открыт - выбрать его
            //const $Element = Instance.$ListContainer.find('.cm-element[data-hid='+Hid+']');
            Instance.selectFile(Hid);
        }
    }
    
    // --- --- --- --- ---
    /**
     * 0. установить CurrentHid
     * 1. выделить строку в выпадающем списке
     * 2. вставить имя файла в шапке
     * 3. вставить путь файла в шапке ace
     * 4. акивировать нужный ace
     * 5. выделить ветку
     */
    selectFile(hid){
        const Instance = this;
        
        Instance.CurrentHid = hid;
        
        this.$ListContainer.find('.cm-element:not(.cm-template)').removeAttr('active').filter(function(){
            return $(this).data('hid') === Instance.CurrentHid;
        }).attr('active','active').map(function(index, element){
            const Parent = $(element).data('cm-parent');
            const Name = $(element).data('cm-name');
            Instance.$CurrentContainer.find('.cm-text').text(Name);
            Instance.$AceHeader.text(Parent+'/'+Name);
        });
        
        this.$AceContainer.find('.cm-ace').removeAttr('active');
        this.$AceContainer.find('.cm-ace#'+Instance.CurrentHid).attr('active','active').data('cm-ace').Editor.focus();
        
        this.$Tree.find('li').children('.cm-line').removeAttr('active').end().filter(function(){
            return $(this).data('hid') === Instance.CurrentHid;
        })
        .parents('li[data-status]').attr('data-status',1).end()
        .map(function(index,element){
            const $Container = Instance.$TreeContainer;
            const $Element = $(element);
            $Container.animate({
                scrollTop: $Element.offset().top - $Container.offset().top + $Container.scrollTop()
            });        
        }).end()
        .children('.cm-line').attr('active','active');
    }
    

    // --- --- --- --- ---
    nextFile(direct){
        const Instance = this;
        
        let Index = this.$ListContainer.find('.cm-element:not(.cm-template)').filter(function(){
            return $(this).data('hid') === Instance.CurrentHid;
        }).index();
        
        direct === -1 ? Index-- : Index++;
        if(Index === 0 || Index > this.ListCount) return;
        
        this.selectFile(this.$ListContainer.find('.cm-element').eq(Index).data('hid'));
    }
    
    // --- --- --- --- ---
    /**
     * Закрепить файл в списке
     */
    pushFile($element){
        const Hid = $element.attr('data-hid');
        console.log('push',Hid);
        
        let $List = this.$ListContainer.find('.cm-element:not(.cm-template)');
        let Count = this.$ListContainer.find('.cm-element.cm-push').length;
        console.log(Count,$List);
        
        if($element.hasClass('cm-pushed')){
        }
        else{
            //if(Count) $element.detach().insertAfter($List.eq(Count));
            //else $element.detach().prependTo($List);
        }
        
        $element.toggleClass('cm-pushed');
    }

    // --- --- --- --- ---
    closeFile(hid){
        console.log(hid);
        
        
        
        
        return;
        const Instance = this;
        if($element.hasClass('cm-pushed'))return;
        
        const Hid = $element.attr('data-hid');
        console.log('close',Hid);
        
        //let PreHid;
        let $List = this.$ListContainer.find('.cm-element:not([id])');
        let Count = $List.length;
        let Index = $element.index();
        
        Instance.selectFile($List.eq(Index-1));
        
        if(Index !== Count) Instance.selectFile($List.eq(Index));
        else Instance.selectFile($List.eq(Index-2));
        
        $element.remove();
        Instance.checkCurrent();
    }
    
    // --- --- --- --- ---
    saveFile(ace){
        const Instance = this;
        
        const Hid = ace.$Tag.data('hid');
        const Content = ace.Editor.getValue();
        //const Content = LZW.compress(ace.Editor.getValue());
        console.log(Hid,Content,Content.length);
        
        var Int16 = new Int16Array(Content.length);
        for (let i = 0; i < Content.length; i++){
            Int16[i] = Content.charCodeAt(i);
        }
        
        $.ajax({
           url: 'res/res/ide.php',
           type: 'POST',
           contentType: 'application/octet-stream',  
           data: Int16,
           processData: false
        });
        
        console.log(Int16);
        
        /*
        console.log(Int16);
        return;
        
        let Compr = [];
        for (let i = 0; i < Content.length; i++){
            Compr.push(Content.charCodeAt(i));
            //console.log(Content.charCodeAt(i));
        }
        Compr = Compr.join('');
        
        console.log('Compr',Compr,Compr.length,Content.length);
        console.log(new Blob([Content],{type: "image/png"}));
        
//[oReq.response], {type: "image/png"}        
        
//codePointAt()        
//        return;
        const _success = function(data){
            Instance.cursor(false);            
            Instance.Message.success(data.message);
        };
        
        const _error = function(data){
            Instance.cursor(false);
            Instance.Message.error(data.message);
        };
        
        Instance.cursor();
        Instance.ajax({
            m : 'save',
            hid : Hid,
            //content : Compr
            //content : new Blob([Content],{type: "image/png"})
            content : Int16
        },_success,_error);
        */
    }
   
    // --- --- --- --- ---
    /**
     * @param byte mode - режим
     *      1 - get file by hid
     *      2 - 
     *      3 - 
     * @param string[32] hid
     * @param string data
     * @param function _success
     * @param function _error
     */
    ajax(mode,hid,data,_success,_error){
        
        new Req([mode,hid,data]).ajaxBinary(16,{
            method : 'post',
            url : 'res/res/ide.php',
        },_success,_error);
        return;
        
        let Int16 = new Int16Array(13);
        
        let index = 0;
        Int16[index++] = 1;
        Int16[index++] = 1;
        Int16[index++] = 77;
        
        const Str = '12345';
        Int16[index++] = 3;
        Int16[index++] = 5;
        for (let i = 0; i < Str.length; i++) Int16[index++] = Str.charCodeAt(i);
        
        Int16[index++] = 1;
        Int16[index++] = 1;
        Int16[index++] = 13;
        
        console.log(Int16);
        
        /*
        data = data || '';
        console.log(mode,hid,data);
        
        let Int16 = new Int16Array(1 + 32 + 1 + this.Project.length + data.length);
        
        // mode
        let D = 0;
        Int16[D] = mode;
        
        // hid
        D = 1;
        for (let i = 0; i < 32; i++){
            Int16[i+D] = hid.charCodeAt(i);
        }
        
        // project length
        D = 1 + 32;
        Int16[D] = this.Project.length;
        
        // project
        D = 1 + 32 + 1;
        for (let i = 0; i < this.Project.length; i++){
            Int16[i+D] = this.Project.charCodeAt(i);
        }
        
        //data
        D = 1 + 32 + 1 + this.Project.length;
        for (let i = 0; i < data.length - D; i++){
            Int16[i+D] = data.charCodeAt(i);
        }
        
        console.log(Int16);
        */
        
        $.ajax({
            method : 'post',
            async : true,
            url : 'res/res/ide.php',
            //dataType : 'json',
            //data : $.extend(data,{
            //    'project' : this.Project
            //})
            data : Int16,
            contentType: 'application/octet-stream',
            processData: false
        })
        .done(function(data){
            data.status == 1 ? _success(data) : _error(data);
        })
        .fail(function(data, textStatus, jqXHR){
            _error(data);
        });
    }
}