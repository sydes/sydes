$(document).ready(function () {
    stickMenu($('#menu'));

    if ($('.tab-container .col-xs-2').width() < 183) {
        $('.tab-container .col-xs-2').toggleClass('col-xs-2 col-xs-3');
        $('.tab-container .col-xs-10').toggleClass('col-xs-10 col-xs-9')
    }

    $('[data-toggle="tooltip"]').tooltip();
    $("[data-toggle=popover]").popover({html: true});

    $('#checkall').click(function () {
        $('.ids').prop('checked', $(this).prop('checked'))
    });
    $('select.goto').change(function () {
        location.href = $(this).data('url')+$(this).val()
    });
    $('.submit').click(function () {
        ajaxFormApply()
    });

    $('.modal').on('show.bs.modal', function (e) {
        var size = $(e.relatedTarget).data('size'), dialog = $(this).find('.modal-dialog');
        dialog.removeClass('modal-sm modal-lg');
        if (size) {
            dialog.addClass('modal-'+size)
        }
        setTimeout(modalPosition, 10)
    }).on('loaded.bs.modal', function () {
        modalPosition()
    }).on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal')
    });
});

var editorBuffer = '';
$(document).on('click', '.lazy.ckeditor', function () {
    var editor = CKEDITOR.replace(this, {
        toolbar: [
            ['Source', 'Save'],
            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Blockquote'],
            ['Maximize', 'ShowBlocks', 'Image']
        ],
        height: 200,
        allowedContent: true
    });

    editor.on('blur', function () {
        if (editorBuffer != editor.getData()) {
            for (var i in CKEDITOR.instances)
                CKEDITOR.instances[i].updateElement();
            $('.lazy.ckeditor').change()
        }
        editorBuffer = editor.getData();
    });
    editorBuffer = editor.getData();
});

$(document).on('click', '[data-dismiss="widget"]', function () {
    $(this).parents('.widget').remove();
});

$(document).on('click', '.apply-modal', function () {
    var form = $('form[name="modal-form"]');
    if (form.length) {
        $.ajax({
            url: form.prop('action'),
            data: form.serialize()
        })
    }
});

var ua = navigator.userAgent.toLowerCase(),
    isIE = (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1),
    isSafari = ua.indexOf("safari") != -1,
    isGecko = (ua.indexOf("gecko") != -1 && !isSafari);
if (isIE || isSafari) {
    addHandler(document, "keydown", hotSave)
} else {
    addHandler(document, "keypress", hotSave)
}

function ajaxFormApply() {
    if (window.codemirror) window.codemirror.save();
    if (typeof CKEDITOR != 'undefined') {
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }
    var form = $('form[name="main-form"]');
    if (form.length) {
        $.ajax({
            url: form.prop('action'),
            data: form.serialize()+'&act=apply'
        })
    }
}

function addHandler(object, event, handler, useCapture) {
    if (object.addEventListener) object.addEventListener(event, handler, useCapture);
    else if (object.attachEvent) object.attachEvent('on'+event, handler);
    else object['on'+event] = handler;
}

function hotSave(evt) {
    evt = evt || window.event;
    var key = evt.keyCode || evt.which;
    key = !isGecko ? (key == 83 ? 1 : 0) : (key == 115 ? 1 : 0);
    if (evt.ctrlKey && key) {
        if (evt.preventDefault) evt.preventDefault();
        evt.returnValue = false;
        ajaxFormApply();
        window.focus();
        return false;
    }
}

function toBuffer(fileUrl, data, allFiles) {
    var body = '';
    for (var key in allFiles) {
        if (allFiles.hasOwnProperty(key)) {
            body += '<pre>'+allFiles[key]['url']+'<br>&lt;img src="'+allFiles[key]['url']+'"></pre>';
        }
    }
    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Ctrl+C</h4></div>' +
        '<div class="modal-body">'+body+'</div>');
    $('#modal').modal('show');
}

function modalPosition() {
    $('.modal').each(function () {
        if ($(this).hasClass('in') == false) {
            $(this).show();
        }
        var content = $(window).height() - 60;
        var header = $(this).find('.modal-header').outerHeight() || 2;
        var footer = $(this).find('.modal-footer').outerHeight() || 2;

        $(this).find('.modal-content').css({
            'max-height': function () {
                return content;
            }
        });

        $(this).find('.modal-body').css({
            'max-height': function () {
                return (content - (header+footer));
            }
        });

        $(this).find('.modal-dialog').css({
            'margin-top': function () {
                return -($(this).outerHeight() / 2);
            },
            'margin-left': function () {
                return -($(this).outerWidth() / 2);
            }
        });
        if ($(this).hasClass('in') == false) {
            $(this).hide();
        }
    });
}

$(document).on('mousedown', '.field-date', function () {
    if (!$(this).hasClass('hasDatepicker')) {
        $(this).datepicker({
            dateFormat: 'dd.mm.yy'
        })
    }
});
