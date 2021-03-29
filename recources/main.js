import Project from './js/Project.class.js';
import Message from './js/Message.class.js';

const message = new Message();
const project = new Project(message);

$(document).ready(function(){
    const
        $Container = $('#cm-projects'),
        $Panel = $('#cm-add-project'),
        $Template = $('#cm-project-template'),
        $Form = $('#cm-project-form').on('submit',function(e){
            e.preventDefault();
            _add_project();
        }),
        $Error = $('#cm-error'),
        $Confirm = $('#cm-confirm')
            .find('.cm-yes').on('click',function(){
                const _fun = $Confirm.cmOk;
                _fun();
                _confirmMessageOff();
            })
            .end().find('.cm-no').on('click',function(){
                _confirmMessageOff();
            }).end(),

        _capitalize = function(s){
            return s[0].toUpperCase() + s.substring(1);
        },

        _ajax = function(url, data, _success, _error){
            $.ajax({
                method : 'post',
                url : 'res/res/' + url,
                data : data,
                dataType : 'json'
            })
            .done(function(data){
                data.status == 1 ? _success(data) : _error(data);
            })
            .fail(function(data, textStatus, jqXHR){
                //console.log('fail',data, textStatus);
                _error(data);
            });
        },

        _errorMessage = function(message){
            $Error.find('.cm-text').text(message).end().addClass('cm-show cm-opacity');

            setTimeout(function(){
                $Error.removeClass('cm-opacity');
                setTimeout(function(){ $Error.removeClass('cm-show'); },500);
            },1500);
        },

        _confirmMessage = function(message,_ok){
            $Confirm.cmOk = _ok;
            $Confirm
                .find('.cm-text').text(message)
                .end().addClass('cm-show cm-opacity');
                $(document)._keyup = $(document).keyup;
                $(document).on('keyup',function(e){
                    if(e.keyCode == 27) _confirmMessageOff();
                });
        },

        _confirmMessageOff = function(){
            $(document).off('keyup');
            $Confirm.removeClass('cm-opacity');
            setTimeout(function(){ $Confirm.removeClass('cm-show'); },500);
        },

        _add_project = function(){
            _success = function(data){
                $Panel.removeClass('cm-show');
                $Template.clone(true,true)
                    .attr('id','cm-project-'+data.data.name.replace(/ /g,"_"))
                    .attr('href',$Template.attr('href')+data.data.name)
                    .find('.cm-name').text(data.data.name)
                    .end().appendTo($Container);
            };

            _error = function(data){
                 _errorMessage(data.error);
            };

            _ajax('project.php', $Form.serializeArray(), _success , _error);
        },

        _del_project = function(name){
            _success = function(data){
                $('#cm-project-'+name.replace(/ /g,"_")).remove();
            };

            _error = function(data){
                 _errorMessage(data.error);
            };

            _ajax('project.php',{
                m : 'del',
                name : name
            },_success , _error);
        },

        _parser_project = function(name, _success){
            _ajax('project.php',{
                m : 'check',
                name : name
            },_success , _error);
        };

    // --- --- --- --- ---
    $('header .cm-add').on('click',function(e){
        project.new();
        

        //$Panel.addClass('cm-show');
    });

    $('#cm-add-project .cm-close').on('click',function(e){
        $Panel.removeClass('cm-show');
    });

    $('input').each(function(e){
        $(this).attr('oninvalid',"this.setCustomValidity('qaz')").attr('oninput',"this.setCustomValidity('')");
    });

    /*$('.cm-project').on('dblclick',function(){
        //window.location.href = $(this).attr('href');
        //window.location.href = document.location.href + '/project/' + $(this).find('.cm-name').text();
        //window.open(document.location.href + '/project/' + $(this).find('.cm-name').text());
    });*/

    $('.cm-project .cm-del').on('click',function(){
        const Name = $(this).closest('.cm-project').find('.cm-name').text();
        const Message = _capitalize($("meta[name='local.qDelProject']").attr('content'))+' '+Name+'?';

        _confirmMessage(Message,function(){
             _del_project(Name);
        });
    });

});