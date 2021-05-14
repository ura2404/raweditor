export default class Local {
    constructor(){
    }

    message(tag){
        const Tag = 'local.' + tag;
        const Message = $("meta[name='" + Tag + "']").attr('content');
        return Message ? Message : Tag;
    }
}