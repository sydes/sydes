$(document).ajaxSend(function () {
    $('html').css('cursor', 'wait');
}).ajaxSuccess(function (e, xhr) {
    if (localStorage['debug'] == 1) {
        console.log(xhr.responseText)
    }

    if (xhr.getResponseHeader('Content-Type') == 'application/json') {
        var response = JSON.parse(xhr.responseText);

        if ('notify' in response) {
            syd.notify(response.notify.message, response.notify.status)
        }

        if ('alerts' in response) {
            response.alerts.forEach(function(alert) {
                syd.alert(alert.message, alert.status)
            })
        }

        if ('reload' in response) {
            window.location.reload()
        }

        if ('redirect' in response) {
            location.href = response.redirect
        }

        if ('modal' in response) {
            syd.modal(response.modal)
        }
    }
}).ajaxError(function () {
    $('html').css('cursor', 'auto');
    syd.notify('AJAX 404 (Not Found)', 'danger')
}).ajaxComplete(function () {
    $('html').css('cursor', 'auto')
});
