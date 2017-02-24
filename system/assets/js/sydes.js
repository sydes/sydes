var syd = syd || {'settings': {}, 'l10n': {}, 'csrf': {}};

(function ($) {

/**
 * Shows notice on top right corner of window
 * @param message
 *   Message, only text
 * @param status
 *   Status, may be one of 'success', 'info', 'warning', 'danger'
 * @param delay
 *   Delay in milliseconds before hiding
 */
syd.notify = function (message, status, delay) {
    status = status || 'info';
    delay = delay || 4000;
    if (message != null) {
        $('#notify').append($('<li class="'+status+'">'+message+'</li>').delay(delay).slideUp());
    }
};

/**
 * Shows dismissible alert box
 * @param message
 *   Message, text or html
 * @param status
 *   Status, may be one of 'success', 'info', 'warning', 'danger'
 */
syd.alert = function (message, status) {
    var duplicate = false;
    $('.alert').each(function () {
        if ($(this).text() == '×'+message) {
            duplicate = true;
        }
    });
    status = status || 'info';
    if (message != null && !duplicate) {
        $('#alerts').append($('<div class="alert alert-'+status+' alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>'+message+'</div>'));
    }
};

/**
 * Creates new modal and show it
 * @param params
 *   Array with size, title, body and footer keys
 */
syd.modal = function (params) {
    params.size = params.size || '';

    var id = 'modal-loaded',
        title_html = '<div class="modal-header"><div class="modal-title">'+params.title+'</div>\
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
            <span aria-hidden="true">&times;</span></button></div>',
        body_html = params.body ? '<div class="modal-body">'+params.body+'</div>' : '',
        footer_html = params.footer ? '<div class="modal-footer">'+params.footer+'</div>' : '',
        modal = '<div class="modal fade" id="'+id+'" tabindex="-1" role="dialog">'+
            '<div class="modal-dialog '+params.size+'" role="document">'+
        '<div class="modal-content">'+title_html+body_html+footer_html+'</div></div></div>';
    $('body').append(modal);
    $('#'+id).modal('show').on('hidden.bs.modal', function () {
        $(this).remove();
    });
};

/**
 * Translate strings to the page language
 * @param str
 *   A string containing the string to translate.
 */
syd.t = function (str) {
    if (syd.l10n && syd.l10n[str]) {
        return syd.l10n[str];
    } else {
        return str;
    }
};

/**
 * Creates a random string of a certain length
 * @param length
 */
syd.token = function (length) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz',
        string = '';
    for (var i = 0; i < length; i++) {
        string += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return string;
};

/**
 * Execute code via adding in DOM
 * @param code
 * @param doc
 */
syd.eval = function (code, doc) {
    doc = doc || window.document;

    var script = doc.createElement("script");

    script.text = code;
    doc.head.appendChild(script).parentNode.removeChild(script);
};

$(document).on('click', '[data-load=modal]', function () {
    var size = $(this).data('size') || 'md';

    $.get($(this).attr('href'), function(data) {
        syd.modal({
            title: '',
            body: data,
            size: 'modal-'+size
        });
    });

    return false;
});

})(jQuery);
