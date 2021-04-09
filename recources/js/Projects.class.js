export default class Project {
    constructor(message,local){
        const Instance = this;

        this.Message = message;
        this.Local = local;

        this.$Projects = $('#cm-projects');
        this.$Template = $('#cm-project-template');
        this.$Panel = $('#cm-new-project');
        this.$Form = Instance.$Panel.find('form').on('submit',function(e){
            e.preventDefault();
            Instance.add();
        });
    }

    init(){
        const Instance = this;
        const Message = Instance.Local.message('requiredField');

        $('header .cm-add').on('click',function(e){
            Instance.new();
        });

        this.$Panel.find('.cm-action-close').on('click',function(e){
            Instance.cancel();
        });

        this.$Projects.find('.cm-project .cm-action-del').on('click',function(e){
            Instance.delete($(this).closest('.cm-project'));
        });
    }

    expand(){
        $('body').addClass('cm-expand');
    }

    new(){
        this.showPanel();
    }

    cancel(){
        this.hidePanel();
    }

    delete($node){
        const Instance = this;

        this.Message.confirm(this.Local.message('qDelProject')+' '+ $node.attr('data-name') + '?',function(){
            Instance.del($node);
        });
    }

    // --- --- --- --- ---
    add(){
        const Instance = this;

        const _success = function(data){
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

        this.ajax(this.$Form.serializeArray(), _success, _error);
    }

    del($node){
        const Instance = this;
        const Name = $node.attr('data-name');

        const _sucess = function(data){
            $node.remove();
        };

        const _error = function(data){
            Instance.Message.error(data.error);
        };

        this.ajax({
            m : 'del',
            name : Name
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
        Instance.$Panel.addClass('cm-show cm-opacity').find("input:not([type='hidden'])").val('');
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

