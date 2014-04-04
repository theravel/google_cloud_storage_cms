$(function(){

    var timer;
    function showResult(success) {
        if (success) {
            $('#success-pages, #error-pages').fadeOut(0, function() {
                $('#success-pages').fadeIn(500);
            });
        } else {
            $('#success-pages, #error-pages').fadeOut(0, function() {
                $('#error-pages').fadeIn(500);
            });
        }
        timer = setTimeout(function() {
            $('#success-pages, #error-pages').fadeOut(500);
        }, 4000);
    }

    $('.admin-pages table tbody').sortable({
        placeholder: 'ui-state-highlight',
        cancel: '.non-draggable',
        stop: function(event, ui) {
           var order = [];
           $('.admin-pages table tr:not(.non-draggable)').each(function(){
               order.push($(this).find('.hidden').val());
           });
           clearTimeout(timer);
           $.ajax({
               url: '/admin/pagesSort',
               type: 'POST',
               dataType: 'json',
               data: {order: order},
               success: function(data) {
                   showResult(data.success);
               },
               error: function() {
                   showResult(false);
               }
           });
        }
    });

    $('.menu-enabled').on('change', function(){
        clearTimeout(timer);
        $.ajax({
            url: '/admin/pagesMenu',
            type: 'POST',
            dataType: 'json',
            data: {
                id: $(this).attr('data-id'),
                enabled: $(this).is(':checked')
            },
            success: function(data) {
                showResult(data.success);
            },
            error: function() {
                showResult(false);
            }
        });        
    });

    $('.entity-delete').on('click', function() {
        var row = $(this).parents('tr');
        if (confirm($(this).attr('data-confirmation'))) {
            $.ajax({
                url: $(this).attr('data-href'),
                type: 'POST',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-id')
                },
                success: function(data) {
                    row.remove();
                    showResult(data.success);
                },
                error: function() {
                    showResult(false);
                }
            }); 
        }
    });

    var textarea = $('#page-content');
    if (textarea.length) {
        textarea.ckeditor(
            function() {},
            {
                filebrowserUploadUrl: $('#upload-file-url').val(),
                filebrowserImageUploadUrl: $('#upload-image-url').val()
            }
        );
    }

    $(document).on('mouseenter', '.cke_dialog_ui_fileButton:visible', function(){
        var element = $('iframe.cke_dialog_ui_input_file:visible').contents().find('form[enctype="multipart/form-data"]');
        if (!element.length || element.data('processed')) {
            return;
        }
        element.data('processed', true);
        var action = element.attr('action').split('?');
        action.pop();
        element.attr('action', action.join('?'));
    });
});