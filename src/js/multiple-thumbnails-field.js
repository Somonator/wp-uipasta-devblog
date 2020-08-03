jQuery(document).ready(function ($) {
    let files_modal,
        $field = $('#multiple-thumbnails'),
        $list_thumbs = $field.find('.thumbnails'),
        $add_btn = $field.find('.add-thumbnails');

    $add_btn.click(function(event) {
        event.preventDefault();

        if (files_modal) {
            files_modal.open();
            return;
        }

        files_modal = wp.media.frames.file_frame = wp.media({
            title: mf_text.title_popup,
            library: {
                type: 'image'
            },
            button: {
                text: mf_text.btn_select_popup
            },
            multiple: 'add'
        });
        
        files_modal.on('open', function() {
            files_modal.state().get('selection').reset();

            $field.find('input').each(function() {
                let val = $(this).val(),
                    file = wp.media.attachment(val);

                files_modal.state().get('selection').add(file);
            });
        }).on('select', function () {
            let selection = files_modal.state().get('selection');

            $list_thumbs.empty();
            selection.map(function(attachment) {
                attachment = attachment.toJSON();

                $('<div class="item"></div>')
                    .append('<input type="hidden" name="portfolio-thumbnails[]" value="' + attachment.id + '">')
                    .append('<div class="remove"><span class="dashicons dashicons-no-alt"></span></div>')
                    .append('<img src="' + (attachment.sizes?.medium?.url !== undefined ? attachment.sizes.medium.url : attachment.url) + '" alt="">')
                    .appendTo($list_thumbs);
            });
        }).open();
    });

    $field.delegate('.remove', 'click', function(event) {
        event.preventDefault();

        $(this).parents('.item').remove();        
    });
});