import Projects from './js/Projects.class.js';
import Message from './js/Message.class.js';
import Local from './js/Local.class.js';


const message = new Message();
const local = new Local();
const projects = new Projects(message,local);

$(document).ready(function(){
    projects.init();
});