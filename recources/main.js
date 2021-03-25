$(document).ready(function(){
    const $Container = $('#cm-projects');
    const $Panel = $('#cm-add-project');

    const _ok = function(data){
        $Template = $('#cm-project-template');
        $Panel.removeClass('cm-show');
        $Template.clone(true.true).removeClass('cm-template').appendTo($Container);
    };

    const _error = function(){
        $Panel.removeClass('cm-show');
    };

    const _add_project = function(){
        $.ajax({
            method : 'post',
            url : 'res/res/post.php',
            data : $('.cm-form').serializeArray(),
            dataType : 'json'
        })
        .done(function(data){
            console.log(data);
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

    $('#cm-add-project .cm-add').on('click',function(e){
        console.log('add');
        //_add_project();
        $('.cm-form').submit(function(e){
            //console.log(e);
        });
    });

});
