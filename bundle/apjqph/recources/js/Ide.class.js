export default class Ide {
    constructor($tag,$message){
        const Instance = this;
        
        this.$Ide = $tag;
        this.Message = $message;
        
        this.Name = this.$Ide.attr('data-name');
        this.$Direct = this.$Ide.find('.cm-ide-direct');
        this.$Tree = this.$Ide.find('.cm-ide-tree');
        this.$Splitter = this.$Ide.find('.cm-splitter');
        
        this.$NodeTemplate = $('#cm-node-template');
        
        this.$Current = $('#cm-current');
        this.$CurrentContainer = this.$Current.find('.cm-container');
        
        this.$List = $('#cm-list');
        this.$ListContainer = this.$List.find('.cm-container');
        this.$ListTemplate = $('#cm-list-template');
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
            /*.find('li i.cm-folder').on('click',function(e){          // click на иконке папки
                e.preventDefault();
                Instance.treeNodeExpand($(this).closest('li'));
            })*/
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
                Instance.selectFile($(this));
            })
            .find('.cm-close').on('click',function(e){
                e.stopPropagation();
                console.log('close');
                Instance.closeFile($(this).parent());
            }).end()
            .find('.cm-push').on('click',function(e){
                e.stopPropagation();
                console.log('push');
                Instance.pushFile($(this).parent());
            });
            
        /**
         * открыть / закрыть список файлов
         */
        const _list = function(){
            Instance.$Current.toggleClass('cm-opend');
            Instance.$List.toggleClass('cm-opend');
        };
        
        this.$Current.find('i').on('click',_list);
        this.$List.on('click',_list);
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
                
                Instance.$NodeTemplate.clone(true,true).removeAttr('id').map(function(index, element){
                    $(this).attr('data-hid',Hid).children('div').attr('title',Name).children('.cm-text').text(Name);
                    
                    for(let j=0; j<List[i].level-1; j++){
                        $Tab.clone(true,true).prependTo($(this).children('.cm-line'));
                    }
                    
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
     * visible or not visible current
     */
    checkCurrent(){
        console.log('list container count',this.$ListContainer.children('div:not([id])').length);
        
        if(this.$ListContainer.children('div:not([id])').length){
            this.$Current.addClass('cm-visible');
            this.$List.addClass('cm-visible');
        }
        else {
            this.$Current.removeClass('cm-visible');
            this.$List.removeClass('cm-visible');
        }
    }
    
    // --- --- --- --- ---
    /**
     * open file from tree node
     */
    treeNodeOpen($node){
        const Instance = this;
        
        const Name = $node.find('.cm-text').text();
        const Hid = $node.attr('data-hid');
        
        const $ListContainer = this.$List.find('.cm-container');
        
        if(
            $ListContainer.find('.cm-element:not([id])').map(function(){
                return $(this).attr('data-hid') === Hid;
            }).get().every(function(val){ return !val; }) 
        ){
            new Promise(function(resolve, reject){
                resolve(Instance.$ListTemplate.clone(true,true).removeAttr('id').attr('data-hid',Hid).find('.cm-text').text(Name).end().appendTo(Instance.$ListContainer));
            }).then($element => {
                Instance.$CurrentContainer.text(Name);
                Instance.selectFile($element);
                Instance.checkCurrent();
            });
        }
        else{
            const $Element = Instance.$ListContainer.find('.cm-element[data-hid='+Hid+']');
            Instance.selectFile($Element);
        }
    }
    
    // --- --- --- --- ---
    selectFile($element){
        console.log('select',$element);
        const Hid = $element.attr('data-hid');
        console.log('select',Hid);
        
        const Instance = this;
        
        this.$ListContainer.find('.cm-element:not([id])').removeAttr('active');
        $element.attr('active','active');
        this.$CurrentContainer.text($element.text());
    }
    
    // --- --- --- --- ---
    pushFile($element){
        const Hid = $element.attr('data-hid');
        console.log('push',Hid);
        
        let $List = this.$ListContainer.find('.cm-element:not([id])');
        let Count = this.$ListContainer.find('.cm-element.cm-push').length;
        console.log(Count,$List);
        
        if($element.hasClass('cm-pushed')){
        }
        else{
            if(Count) $element.detach().insertAfter($List.eq(Count));
            else $element.detach().prependTo($List);
        }
        
        $element.toggleClass('cm-pushed');
    }

    // --- --- --- --- ---
    closeFile($element){
        const Instance = this;
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
    ajax(data,_success,_error){
        $.ajax({
            method : 'post',
            async : true,
            url : 'res/res/ide.php',
            dataType : 'json',
            data : $.extend(data,{
                'name' : this.Name
            })
        })
        .done(function(data){
            data.status == 1 ? _success(data) : _error(data);
        })
        .fail(function(data, textStatus, jqXHR){
            _error(data);
        });
    }

}