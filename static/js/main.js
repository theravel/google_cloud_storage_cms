$(function() {
    $('.nav li').on('mouseenter mouseleave', function() {
        var element = $(this).find('> .submenu');
        element.removeClass('right').fadeToggle(300);
        if (element.length && element.offset().left + element.width() > $(window).width()) {
            element.addClass('right');
        }
    });
});