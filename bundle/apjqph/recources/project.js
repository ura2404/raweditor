import Ide from './js/Ide.class.js';

const ide = new Ide($('#cm-ide'));

$(document).ready(function(){
    // это так не работает
    //ace.require("ace/ext/language_tools");
    //ace.require("ace/ext/emmet");
    
    ide.init();
});