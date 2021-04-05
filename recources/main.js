import Projects from './js/Projects.class.js';
//import Project from './js/NewProject.class.js';
import Message from './js/Message.class.js';
import Local from './js/Local.class.js';


const message = new Message();
const local = new Local();
const projects = new Projects(message,local);

$(document).ready(function(){
    projects.init();


/*
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
*/
});