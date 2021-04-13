export default class Ide {
    constructor($tag,message){
        const Instance = this;

        this.$Ide = $tag;
        this.Name = this.$Ide.attr('data-name');
        this.$Direct = this.$Ide.find('.cm-project-direct');
        this.$Tree = this.$Ide.find('.cm-project-tree');
        this.$Splitter = this.$Ide.find('.cm-splitter');
        this.Message = message;
    }

    // --- --- --- --- ---
    init(){
        const Instance = this;

        this.$Direct.find('i.cm-action-tree').on('click',function(){
            $(this).toggleAttr('active');          // атрибут кнопки
            Instance.$Tree.toggleAttr('visible');  // атрибут дерева
            Instance.$Splitter.toggleAttr('visible');
        });

        this.$Tree
            .find('ul li i.cm-action').on('click',function(e){
                e.preventDefault();
                Instance.treeNodeExpand($(this).closest('li'));
            })
            .end()
            .find('.cm-line').on('click',function(){
                Instance.$Tree.find('.cm-line').removeAttr('active');
                $(this).toggleAttr('active');          // атрибут кнопки
            });

        this.$Tree
            .resizable({
                handleSelector: ".cm-splitter",
                resizeHeight: false
            });
    }

    // --- --- --- --- ---
    treeNodeExpand($node){
        const Instance = this;

        const $_expand = function(){
            $node.attr('data-status',function(index, value){
                return value =='0' ? '1' : '0';
            });
        };

        const $_addNodes = function(data){
        }

        const _success = function(data){
            $_expand();
            $_addNodes(data);
        };

        const _error = function(data){
            Instance.Message.error(data.message);
        };

        if($node.find('ul').length) $_expand();
        else{
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