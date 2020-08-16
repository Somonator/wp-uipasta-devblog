(function ($) {
    /* edit comment */
    $(document).ready(function() {
        let move_fields = $('[data-move]'),
            common_wrap = $('.comment-fields');
        
        if (move_fields.length > 0) {
            move_fields = $('<div class="author-fields"></div>').hide().append(move_fields);
            common_wrap.append(move_fields);

            $('#comment').click(function() {
                move_fields.slideDown(300);
            });            
        }
    });
})(jQuery);