$.ajaxPrefilter(function(s) {
    if (!s.crossDomain && csrfMethod(s.type)) {
        s.data = s.data ? s.data+'&' : '';
        s.data += 'csrf_name='+syd.csrf.name+'&csrf_value='+syd.csrf.value;
    }
});

$(document).ajaxSend(function () {
    syd.wait()
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

        if (response.script && !s.crossDomain) {
            syd.eval(response.script);
        }

        syd.ajaxCallbacks.fire(response);
    }
}).ajaxError(function () {
    syd.notify('AJAX Error', 'danger')
}).ajaxComplete(function () {
    syd.done()
});
