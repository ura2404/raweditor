export default class Project {
    constructor(message){
        const Instance = this;

        this.Message = message;
        this.$Projects = $('#cm-projects');
        this.$Template = $('#cm-project-template');
        this.$Panel = $('#cm-new-project');
        this.$Form = Instance.$Panel.find('form').on('submit',function(e){
            e.preventDefault();
            Instance.add();
        });
    }

    new(){
        this.showPanel();
    }

    cancel(){
        this.hidePanel();
    }

    delete(name){
        this.del(name);
    }

    // --- --- --- --- ---
    add(){
        const Instance = this;

        const _sucess = function(data){
            const $New = Instance.$Template.clone(true,true)
                .attr('id','cm-project-'+data.data.name.replace(/ /g,'_'))
                .attr('href',Instance.$Template.attr('href')+data.data.name)
                .find('.cm-name').text(data.data.name)
                .end().appendTo(Instance.$Projects);
            Instance.hidePanel();
            Instance.Message.success(data.message);
        };

        const _error = function(data){
            Instance.Message.error(data.error);
        };

        this.ajax(this.$Form.serializeArray(), _sucess, _error);
    }

    del(name){
        const Instance = this;

        const _sucess = function(data){
            $('#cm-project-'+name.replace(/ /g,'_')).remove();
        };

        const _error = function(data){
            Instance.Message.error(data.error);
        };

        this.ajax({
            m : 'del',
            name : name
        }, _sucess, _error);
    }

    ajax(data,_success,_error){
        $.ajax({
            method : 'post',
            url : 'res/res/project.php',
            data : data,
            dataType : 'json'
        })
        .done(function(data){
            data.status == 1 ? _success(data) : _error(data);
        })
        .fail(function(data, textStatus, jqXHR){
            _error(data);
        });
    }

    showPanel(){
        const Instance = this;
        Instance.$Panel.addClass('cm-show cm-opacity');
        $(document).on('keyup',function(e){
            if(e.keyCode == 27) Instance.cancel();
        });
    }

    hidePanel(){
        const Instance = this;
        $(document).off('keyup');

        Instance.$Panel.removeClass('cm-opacity');
        setTimeout(function(){
            Instance.$Panel.removeClass('cm-show');
        },500);
    }

}

