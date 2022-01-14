$(document).ready(function () {
    if ($("#company_id").val() != "") {
        var company_id = $("#company_id").val();
        $(".headquarter_class").each(function () {
            $(this).show();
            if ($(this).attr('data-value') !== company_id) {
                // $(this).hide();
            }
        });
        $(".department_class").each(function () {
            $(this).show();
            if ($(this).attr('data-value') !== company_id) {
                // $(this).hide();
            }
        });
        $(".group_class").each(function () {
            $(this).show();
            if ($(this).attr('data-value') !== company_id) {
                // $(this).hide();
            }
        });
    }
});
