import Projects from './js/Projects.class.js';
import Message from './js/Message.class.js';
import Local from './js/Local.class.js';

const projects = new Projects(new Message(),new Local());

$(document).ready(function(){
    projects.init();
});