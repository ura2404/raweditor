export default class Ide {
    constructor($tag,message){
        const Instance = this;
        
        this.$Ide = $tag;
        this.Name = this.$Ide.attr('data-name');
        this.$Direct = this.$Ide.find('.cm-ide-direct');
        this.$Tree = this.$Ide.find('.cm-ide-tree');
        this.$Splitter = this.$Ide.find('.cm-splitter');
        this.$Template = $('#cm-node-template');
        this.Message = message;
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
    }

    // --- --- --- --- ---
    timer(val){
        return new Promise(function(resolve, reject){
            setTimeout(function(){
                resolve();
            },val);
        });
    }

    // --- --- --- --- ---
    cursor(val){
        console.log(val);
        
        const Instance = this;
        return new Promise(function(resolve,reject){
            val ? Instance.timer(100).then(() => $('body').removeClass('waiting')) : $('body').addClass('waiting');
            resolve();
        });
    }

    // --- --- --- --- ---
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
            
            const $Tab = Instance.$Template.find('.cm-tab');
            let $Container = $node.append('<ul></ul>').children('ul');
            
            for(let i=0; i<Object.keys(List).length; i++){
                const Name = List[i].name;
                const Hid = List[i].hid;
                
                Instance.$Template.clone(true,true).removeAttr('id').map(function(index, element){
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
            Instance.cursor(0);
        };
        
        const _error = function(data){
            Instance.Message.error(data.message);
            Instance.cursor(0);
        };
        
        // если есть не пустрой ul (есть дочерние узлы) или data-status = 1 (папка открыта), то считается, что дочерние узлы прогружены и нужно только скрыть/раскрыть
        // иначе нужно подгрузить дочерние узлы
        if($node.find('ul').length || $node.attr('data-status') == '1') _expand();
        else{
            Instance.cursor(1).then(() => {
                Instance.ajax({
                    m : 'node',
                    hid : $node.data('hid')
                },_success,_error);
                
            });
        }
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