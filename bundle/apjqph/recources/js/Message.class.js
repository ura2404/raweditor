export default class Message {
    constructor(){
        const Instance = this;
        this.Timeout = 2000;
        this.$SuccessPanel = $('#cm-success');
        this.$ErrorPanel   = $('#cm-error').find('.cm-container')
            .on('mouseover',function(){
                $(this).addClass('cm-over');
            }).
            on('mouseout',function(){
                $(this).removeClass('cm-over');
            }).end();
        this.$ConfirmPanel = $('#cm-confirm')
            .find('.cm-no').on('click',function(){
                Instance.hide(Instance.$ConfirmPanel);
            })
            .end().find('.cm-yes').on('click',function(){
                const _fun = Instance._ok;
                _fun();
                Instance.hide(Instance.$ConfirmPanel);
            }).end();
    }

    success(message){
        const Instance = this;
        Instance.show(Instance.$SuccessPanel,message,Instance.Timeout);
    }

    error(message){
        const Instance = this;
        Instance.show(Instance.$ErrorPanel,message,Instance.Timeout);
    }

    confirm(message, _ok){
        const Instance = this;
        Instance._ok = _ok;
        Instance.show(Instance.$ConfirmPanel,message);
    }


    // --- --- --- --- ---
    show($container,message,timeout){
        const Instance = this;
        const Message = message ? message[0].toUpperCase() + message.substring(1) : '';

        $container.find('.cm-text').text(Message).end().addClass('cm-show cm-opacity');

        $(document).on('keyup',function(e){
            if(e.keyCode == 27){
                Instance.hide($container);
            }
        });

        if(timeout) setTimeout(function(){
            Instance.hide($container);
        },timeout);
    }

    hide($container){
        const Timer = setInterval(function(){
            if(!$container.find('.cm-container').hasClass('cm-over')){
                clearInterval(Timer);
                $container.find('.cm-text').text('').end().removeClass('cm-opacity');
                setTimeout(function(){
                    $container.removeClass('cm-show');
                },500);
            }
        },500);
        
    }
}

