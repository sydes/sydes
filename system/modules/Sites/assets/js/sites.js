$('[name="localeIn"]').change(function () {
    var h2l = $('[name="host2locale"]').parent();
    if ($(this).val() == 'url') {
        h2l.hide().val('')
    } else {
        h2l.show()
    }
}).change();
