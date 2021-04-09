/** Получение html содержимого вместе с собсвенным html */
jQuery.fn.outerHtml = function(s) {
    return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
};

/** Есть ли атрибут */
jQuery.fn.hasAttr = function(name) {
    return this.attr(name) !== undefined;
};

/** toggleAttr */
jQuery.fn.toggleAttr = function(name) {
    $(this).hasAttr(name) ? $(this).removeAttr(name) : $(this).attr(name,name);
};

/** Курсор в конец input */
$.fn.setCursorPosition = function(pos){
    pos = pos || 'last';
    if(pos === 'last') pos = this.val().length;
    
    this.each(function(index, elem) {
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    });
    return this;
};
