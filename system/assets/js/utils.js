/*
(function($) {
    $.widget("custom.combobox", {
        _create: function() {
            this.wrapper = $('<div>').addClass('custom-combobox input-group').insertAfter(this.element);
            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },
        _createAutocomplete: function() {
            var selected = this.element.children(':selected'),
                value = '',
                placeholder = this.element.attr('title');
            this.input = $('<input>').appendTo(this.wrapper).val(value).attr('id', 'meta-key').attr("placeholder", placeholder).addClass('form-control').autocomplete({
                delay: 0,
                minLength: 0,
                source: $.proxy(this, '_source')
            });
            this._on(this.input, {
                autocompleteselect: function(event, ui) {
                    ui.item.option.selected = true;
                    this._trigger("select", event, {
                        item: ui.item.option
                    });
                    setTimeout(function(){$('#meta-key').change();},100);
                }
            });
        },
        _createShowAllButton: function() {
            var input = this.input,
                wasOpen = false;
            $("<span>").attr("tabIndex", -1).appendTo(this.wrapper).addClass("input-group-btn").html('<button class="btn btn-default" type="button"><span class="caret"></span></button>').mousedown(function() {
                wasOpen = input.autocomplete("widget").is(":visible");
            }).click(function() {
                input.focus();
                if (wasOpen) {
                    return;
                }
                input.autocomplete("search", "");
            });
        },
        _source: function(request, response) {
            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
            response(this.element.children("option").map(function() {
                var text = $(this).text();
                if (this.value && (!request.term || matcher.test(text))) return {
                    label: text,
                    value: text,
                    option: this
                };
            }));
        },
        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });
})(jQuery);
*/

(function($){
    var add, del,
        methods = {
        init : function(options){
            return this.each(function(){
                var tbl = this, tbody = $(this).find('tbody'), cols = $('tr:first td', tbody).size()
                add = '<td><a href="#" class="btn btn-default btn-sm btn-block append-row" title="'+options.append+'"><span class="glyphicon glyphicon-plus"></span></a></td>'
                del = '<td><a href="#" class="btn btn-default btn-sm btn-block remove-row" title="'+options.remove+'"><span class="glyphicon glyphicon-remove"></span></a></td>'
                $('thead tr', this).append($('<th>').css('width', '50'))
                for (i=0; i<$('tr', tbody).size() - 1; i++){
                    $('tr', tbody).eq(i).append(del)
                }
                $('tr:last', this).append('<td>')
                $('<tr>').append('<td colspan="'+cols+'"></td>'+add).appendTo(tbody)

                $(this).on('click', '.append-row', function(){
                    methods.append(this, tbl)
                })
                $(this).on('click', '.remove-row', function(){
                    methods.remove(this, tbl)
                })
            });
        },
        append : function(e, tbl){
            var row = $(e).parents('tr').prev().clone()
            row.find(':input').val('')
            row.insertBefore($('tr:last', tbl))
            $(e).parents('tr').prev().prev().find('td:last').replaceWith(del)
            $(tbl).trigger('append.dt.row')
        },
        remove : function(e, tbl){
            if ($('tbody tr', tbl).size() > 2){
                $(e).parents('tr').remove()
                $(tbl).trigger('delete.dt.row')
            }
        }
    };

    $.fn.dtable = function(method){
        if (methods[method]){
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method){
            return methods.init.apply(this, arguments);
        } else {
            $.error('method '+method+' not found');
        }
    };
})(jQuery);

function stickMenu(menu) {
    if (menu.outerHeight() <= $(window).height()) {
        return;
    }

    var gap = menu.outerHeight() - $(window).height(),
        pos = 0,
        prev = 0;

    $(window).scroll(function () {
        pos = pos - ($(window).scrollTop() - prev);
        pos = Math.max(pos, -gap);
        pos = Math.min(pos, 0);

        menu.css('top', pos);
        prev = $(this).scrollTop();
    });
}
