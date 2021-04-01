export default class Ide {
    constructor($tag){
        const Instance = this;

        this.$Tag = $tag;
        this.$Direct = this.$Tag.find('#cm-direct');
        this.$Tree = this.$Tag.find('#cm-tree > .cm-root');

    }

    init(){
        const Instance = this;

        this.$Direct.find('i.cm-tree').on('click',function(){
            const $El = $(this);
            const Val = 'active';
console.log($El.hasAttr(Val));
            $El.hasAttr(Val) ? $El.removeAttr(Val) : $El.attr(Val,Val);
        });

        this.$Tree.find('li > .cm-action').on('click',function(){
            Instance.treeNodeExpand($(this).parent());
        });
    }

    // --- --- --- --- ---
    treeNodeExpand($node){
        $node.attr('data-status',function(index, value){
            return value =='0' ? '1' : '0';
        });
    }
}