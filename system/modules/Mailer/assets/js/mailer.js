$('[name="use_smtp"]').change(function () {
    var sub = $('.on-smtp');
    if ($(this).is(':checked') && $(this).val() == '0') {
        sub.hide()
    } else {
        sub.show()
    }
}).change();
