
function htmlspecialchars(str) {
    return str.replace('&', '&amp;').replace('"', '&quot;').replace("'", '&#039;').replace('<', '&lt;').replace('>', '&gt;');
}


function hack() {
    var customer_name = $("input[name=client_name]").val();
    var client_code = document.getElementById("client_code").value;
    window.location.href = 'https://stackoverflow.com/search?q=' + customer_name + ' ' + client_code;
}

$(document).ready(function () {
    $('.hasDatepicker').attr('autocomplete', 'off');

    // Restricts input for each element in the set of matched elements to the given inputFilter.
    (function ($) {
        $.fn.inputFilter = function (inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    this.value = "";
                }
            });
        };
    }(jQuery));

    $(".uintTextBox").inputFilter(function(value) {
        return /^\d*$/.test(value); });

    $('.date-picker-ontop').datepicker({
        dateFormat: 'yy/mm/dd',
        beforeShow: function (textbox, instance) {
            var txtBoxOffset = $(this).offset();
            var top = txtBoxOffset.top;
            var left = txtBoxOffset.left;
            var textBoxWidth = $(this).outerWidth();
            console.log('top: ' + top + 'left: ' + left);
                    setTimeout(function () {
                        instance.dpDiv.css({
                            top: top-190, //you can adjust this value accordingly
                            left: left + textBoxWidth//show at the end of textBox
                    });
                }, 0);
        }});
});

// success warning
function sweetAlert(message, type) {
    if(type == 'error') type = 'warning';
    return Swal.fire({
        title: '',
        text: message,
        icon: type,
        timer: 1500
    });
}

