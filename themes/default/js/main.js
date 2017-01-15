$(document).ready(function () {
    // fancyBox
    if (typeof jQuery.fn.fancybox !== "undefined") {
        $('.fancybox, [rel^="lightbox"]').fancybox();
        $('.various').fancybox({
            maxWidth: 940,
            maxHeight: 600,
            fitToView: true,
            autoSize: true
        });
    }

    // back to top
    $('body').append('<a href="#" class="scrollup">Scroll</a>');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 200) {
            $('.scrollup').fadeIn()
        } else {
            $('.scrollup').fadeOut()
        }
    });
    $('.scrollup').click(function () {
        $('html, body').animate({
            scrollTop: 0
        }, 600);
        return false;
    });
});
