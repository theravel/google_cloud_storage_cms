$(function(){
    $('.file-name').on('click', function() {
        var selector = 'div[role=tabpanel][name=info] input:visible:first';
        window.top.opener.$(selector).val($(this).attr('data-url'));
        window.top.close();
	window.top.opener.focus();
        return false;
    });
});