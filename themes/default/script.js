var themeRoot = (function () {
    var s = document.getElementsByTagName('script');
    return s[s.length - 1].src.replace(/\\/g, '/')
        .replace(/\/[^\/]*\/?$/, '')
})();
function includeJs(scriptUrl) {
    document.write('<script src="'+themeRoot+'/'+scriptUrl+'"></script>');
}
function includeCss(scriptUrl) {
    document.write('<link rel="stylesheet" href="'+themeRoot+'/'+scriptUrl+'" media="screen">');
}

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

    // mobile menu
    $('.to-mobile').toMobileMenu();

    // your scripts here

});
// TODO запихнуть в плагин
includeJs('js/mobile-menu.min.js');
includeCss('css/mobile-menu.min.css');
