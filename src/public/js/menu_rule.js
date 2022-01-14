
var list_menu_id = cro_value.rule_action_id_array;

$(document).ready(function () {

    $(".menu_parent").each(function () {
        var menu_id = $(this).attr('data-value');
        var menu_id = parseInt(menu_id);
        if (jQuery.inArray(menu_id, list_menu_id) == -1) {
            $(this).remove();
        }
    });

    $(".menu_main").each(function () {
        var menu_id = $(this).attr('data-value');
        var menu_id = parseInt(menu_id);
        if (jQuery.inArray(menu_id, list_menu_id) == -1) {
            $(this).remove();
        }
    });

    $(".menu_child").each(function () {
        var menu_id = $(this).attr('data-value');
        var menu_id = parseInt(menu_id);
        if (jQuery.inArray(menu_id, list_menu_id) == -1) {
            $(this).remove();
        }
    });

    $("#menu_btn").click(function(){
        $(".main-sidebar").animate({
            width: "toggle"
        });
        $("#sidebar-overlay").addClass("sidebar-overlay-show");
    });

    $("#sidebar-overlay").click(function(){
        $(".main-sidebar").animate({
            width: "toggle"
        });
        $("#sidebar-overlay").removeClass("sidebar-overlay-show");
    });
});
