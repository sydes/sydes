function csrfMethod(method) {
    return (/^(POST|PUT|DELETE|PATCH)$/i.test(method));
}

$.ajaxPrefilter(function(s) {
    if (!s.crossDomain && csrfMethod(s.type)) {
        s.data = s.data ? s.data+'&' : '';
        s.data += 'csrf_name='+syd.csrf.name+'&csrf_value='+syd.csrf.value;
    }
});

$(document).ajaxSend(function () {
    $('html').addClass('ajax-works');
}).ajaxSuccess(function (e, xhr, s) {
    if (localStorage['debug'] == 1) {
        console.log(xhr.responseText)
    }

    if (!s.crossDomain && csrfMethod(s.type) && xhr.getResponseHeader('x-csrf-name')) {
        syd.csrf.name = xhr.getResponseHeader('x-csrf-name');
        syd.csrf.value = xhr.getResponseHeader('x-csrf-value');
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

        if ('redirect' in response) {
            location.href = response.redirect
        }

        if ('modal' in response) {
            syd.modal(response.modal)
        }

        if ('script' in response && !s.crossDomain) {
            syd.eval(response.script)
        }

        if ('console' in response) {
            console.log(response.console)
        }
    }
}).ajaxError(function () {
    syd.notify('AJAX Error', 'danger')
}).ajaxComplete(function () {
    $('html').removeClass('ajax-works');
});
