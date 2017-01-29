var syd = syd || {'settings': {}, 'l10n': {}};

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

// Automatic adds token field to all form
$(document).on('submit', 'form', function () {
    if (!$(this).find('[name="csrf_name"]').length) {
        $(this).append('<input type="hidden" name="csrf_name" value="'+csrf_name+'"><input type="hidden" name="csrf_value" value="'+csrf_value+'">');
    }
});

})(jQuery);
