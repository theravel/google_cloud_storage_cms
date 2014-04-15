$(function(){
    $('.nav li')
        .on('mouseenter', function() {
            $(this).find('> .submenu').fadeIn(300);
        })
        .on('mouseleave', function() {
            $(this).find('> .submenu').fadeOut(300);
        });
});