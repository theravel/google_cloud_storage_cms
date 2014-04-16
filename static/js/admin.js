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

    var getMenu = function() {
        var menu = JSON.parse($('#menu-items').val());
        var processItem = function(item) {
            item.data = item.link;
            item.state = {opened : true};
            item.type = 'menu';
            if (item.children) {
                for (var i = 0; i < item.children.length; i++) {
                    processItem(item.children[i]);
                }
            }
        }
        for (var i = 0; i < menu.length; i++) {
            processItem(menu[i]);
        }
        return menu;
    }

    var treeRootId = 'root';
    var treeElement = $('#menu-tree');
    if (treeElement.length) {
        treeElement.jstree({
            core: {
                animation: 0,
                multiple: false,
                check_callback: true,
                data: [{
                    id: treeRootId,
                    text: $('#menu-root').val(),
                    type: 'root',
                    state: {opened : true},
                    children: getMenu()
                }]
            },
            types: {
                root: {valid_children: ['menu']},            
                menu: {valid_children: ['menu']}
            },
            plugins: ['types', 'dnd']
        });
        var tree = treeElement.jstree(true);
    }
    var currentNode;

    $('#menu-create').on('click', function(){
        var sel = tree.get_selected();
        if (sel.length && sel[0] !== treeRootId) {
            sel = tree.create_node(sel[0], {type: 'menu'});
        } else {            
            sel = tree.create_node(treeRootId, {type: 'menu'});
        }
        if (sel) {
            tree.edit(sel);
        }
    });

    $('#menu-rename').on('click', function(){
        var sel = tree.get_selected();
        if (sel.length) { 
            tree.edit(sel[0]);
        }
    });

    $('#menu-delete').on('click', function(){
        var sel = tree.get_selected();
        if (sel.length) { 
            tree.delete_node(sel);
        }
    });

    $('.index-form').on('submit', function(){
        var raw = tree.get_json();
        var menu = raw[0].children;
        var processItem = function(item) {
            item.link = item.data;
            if (item.children) {
                for (var i = 0; i < item.children.length; i++) {
                    processItem(item.children[i]);
                }
            }
        }
        for (var i = 0; i < menu.length; i++) {
            processItem(menu[i]);
        }
        $('#new-items').val(JSON.stringify(menu));
    });

    treeElement.on('select_node.jstree', function(e, data){
        currentNode = data.node;
        $('#item-link').val(currentNode.data);
        if ($('#item-link').val() === null) {
            $('#item-link').val('0');
            $('#external-link').show().val(currentNode.data);
        } else {
            $('#external-link').hide();
        }
        $('.item-link-block').css('visibility', 'visible');
    });

    $('#item-change').on('click', function(){
        if ($('#item-link').val() === '0') {
            currentNode.data = $('#external-link').val();
        } else {
            currentNode.data = $('#item-link').val();
        }
    });

    $('#is-news').on('change', function(){
        $('#page-short').toggle( $(this).is(':checked') );
    });

    $('#item-link').on('change', function(){
        $('#external-link').toggle($(this).val() === '0');
    });
});