export default class Ide {
    constructor($tag){
        const Instance = this;

        this.$Ide = $tag;
        this.$Direct = this.$Ide.find('.cm-project-direct');
        this.$Tree = this.$Ide.find('.cm-project-tree > ul');
    }

    init(){
        const Instance = this;

        this.$Direct.find('i.cm-action-tree').on('click',function(){
            const Val = 'active';
            Instance.$Ide.hasAttr(Val) ? Instance.$Ide.removeAttr(Val) : Instance.$Ide.attr(Val,Val);
        });

        this.$Tree
            .find('li i.cm-action').on('click',function(){
                Instance.treeNodeExpand($(this).closest('li'));
            });
    }

    // --- --- --- --- ---
    treeNodeExpand($node){
        $node.attr('data-status',function(index, value){
            return value =='0' ? '1' : '0';
        });
    }
}