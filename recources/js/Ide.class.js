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
            .find('ul li i.cm-action').on('click',function(e){          // click на иконке папки
                e.preventDefault();
                Instance.treeNodeExpand($(this).closest('li'));
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
    treeNodeExpand($node){
        const Instance = this;
        
        // установка атрибуда data-status для li (узел папки)
        const _expand = function(){
            $node.attr('data-status',function(index, value){
                return value =='0' ? '1' : '0';
            });
        };
        
        const _addNode = function(data){
            let List = data.data.list;
            var $Container = $node.append('<ul></ul>').children('ul');
            for(let i=0; i<Object.keys(List).length; i++){
                Instance.$Template.clone(true,true)
                    .removeAttr('id')
                    .map(function(index, element){
                        console.log(index, element);
                    })
                    .appendTo($Container);
            }
        };
        
        const _cursor = function(fl){
            const cl = 'waiting';
            fl ? $('body').removeClass(cl) : $('body').addClass(cl);
        };
        
        const _success = function(data){
            _addNode(data);
            _expand();
            _cursor(0);
            $('body').removeClass('waiting');
        };
        
        const _error = function(data){
            Instance.Message.error(data.message);
            _cursor(0);
        };
        
        // если есть не пустрой ul (дочерние узлы) или data-status = 1 (папка открыта), то считается, что дочерние узлы прогружены и нужно только скрыть/раскрыть
        // иначе нужно подгрузить дочерние узлы
        if($node.find('ul').length || $node.attr('data-status') == '1') _expand();
        else{
            _cursor(1);
            this.ajax({
                m : 'node',
                hid : $node.data('hid')
            },_success,_error);
        }
    }

    // --- --- --- --- ---
    ajax(data,_success,_error){
        var Data = $.extend(data,{
            'name' : this.Name
        });
        
        $.ajax({
            method : 'post',
            url : 'res/res/ide.php',
            data : Data,
            dataType : 'json'
        })
        .done(function(data){
            data.status == 1 ? _success(data) : _error(data);
        })
        .fail(function(data, textStatus, jqXHR){
            _error(data);
        });
    }

}