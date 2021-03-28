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

        _errorMessage = function(message){
            $Error.find('.cm-text').text(message).end().addClass('cm-show cm-opacity');

            setTimeout(function(){
                $Error.removeClass('cm-opacity');
                setTimeout(function(){ $Error.removeClass('cm-show'); },500);
            },1500);
        },

        _ok = function(data){
            $Panel.removeClass('cm-show');
            $Template.clone(true.true).find('.cm-name').text(data.data.name).end().removeClass('cm-template').appendTo($Container);
        },

        _error = function(data){
            _errorMessage(data.error);
        },

        _add_project = function(){
            $.ajax({
                method : 'post',
                url : 'res/res/project.php',
                data : $Form.serializeArray(),
                dataType : 'json'
            })
            .done(function(data){
                data.status == 1 ? _ok(data) : _error(data);
            })
            .fail(function(data, textStatus, jqXHR){
                console.log('fail',data, textStatus);
                _error(data);
            });
        },

        _del_project = function(name){
            $.ajax({
                method : 'post',
                url : 'res/res/project.php',
                data : {
                    m : 'del',
                },
                dataType : 'json'
            })
            .done(function(data){
                data.status == 1 ? _ok(data) : _error(data);
            })
            .fail(function(data, textStatus, jqXHR){
                console.log('fail',data, textStatus);
                _error(data);
            });
        };

    // --- --- --- --- ---
    $('header .cm-add').on('click',function(e){
        $Panel.addClass('cm-show');
    });

    $('#cm-add-project .cm-close').on('click',function(e){
        $Panel.removeClass('cm-show');
    });

    $('input').each(function(e){
        $(this).attr('oninvalid',"this.setCustomValidity('qaz')").attr('oninput',"this.setCustomValidity('')");
    });

    $('.cm-project').on('dblclick',function(){
        window.location.href = document.location.href + '/project/' + $(this).find('.cm-name').text();
    });

    $('.cm-project .cm-close').on('click',function(){
        var Name = $(this).closest('.cm-project').find('.cm-name').text();
        _del_project(Name);
    });

});