import Project from './js/Project.class.js';
import Message from './js/Message.class.js';

const message = new Message();
const project = new Project(message);

$(document).ready(function(){

    $('#cm-actions .cm-expand').on('click',function(){
        project.expand();
    });

return;

    // --- --- --- --- ---
    (function(){
        const Message = $("meta[name='local.requiredField']").attr('content');
        $('input').each(function(e){
            $(this).attr('oninvalid',"this.setCustomValidity('"+Message+"')").attr('oninput',"this.setCustomValidity('')");
        });
    })();

    $('header .cm-add').on('click',function(e){
        project.new();
    });

    $('#cm-new-project .cm-close').on('click',function(e){
        project.cancel();
    });

    $('.cm-project .cm-del').on('click',function(){
        const Name = $(this).closest('.cm-project').find('.cm-name').text().trim();
        const Message = $("meta[name='local.qDelProject']").attr('content')+' '+Name+'?';

        message.confirm(Message,function(){
            project.delete(Name);
        });
    });

});