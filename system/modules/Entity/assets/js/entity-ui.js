// Remove empty fields from GET forms
// Author: Bill Erickson
// URL: http://www.billerickson.net/code/hide-empty-fields-get-form/
$('.entity-filter form').submit(function () {
    $(this).find(":input").filter(function () {
        return !this.value;
    }).attr("disabled", "disabled");
    return true;
});
