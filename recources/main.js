$(document).ready(function(){
    const $Container = $('#cm-projects');
    const $Panel = $('#cm-add-project');
    const $Template = $('#cm-project-template');
    const $Form = $('#cm-project-form').on('submit',function(e){
        e.preventDefault();
        _add_project();
    });

    const _ok = function(data){
        $Panel.removeClass('cm-show');
        $Template.clone(true.true).find('.cm-name').text(data.data.name).end().removeClass('cm-template').appendTo($Container);
    };

    const _error = function(){
        $Panel.removeClass('cm-show');
    };

    const _add_project = function(){
        $.ajax({
            method : 'post',
            url : 'res/res/addProject.php',
            data : $Form.serializeArray(),
            dataType : 'json'
        })
        .done(function(data){
            data.status == 1 ? _ok(data) : _error(data);
        })
        .fail(function(data, textStatus, jqXHR){
            console.log('fail',data, textStatus);
            _error();
        });
    };

    // --- --- --- --- ---
    $('header .cm-add').on('click',function(e){
        $Panel.addClass('cm-show');
    });

    $('#cm-add-project .cm-close').on('click',function(e){
        $Panel.removeClass('cm-show');
    });

    /*$('input').each(function(e){
        $(this).attr('oninvalid',"setCustomValidity('обязательное поле')");
    });*/

});