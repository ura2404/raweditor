export default class Project {
    constructor(message){
        this.MessageContainer = message;
        this.Form = $('#cm-add-project');
    }

    new(){
        console.log(this.MessageContainer);
        console.log(this.Form);
        this.formShow();
    }

    add(){
        
    }

    del(){
        
    }

    formShow(){
        this.Form.addClass('cm-show');
    }

}

