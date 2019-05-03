/* let ajax handle all forms */
$('form').submit(function (event) {
    event.preventDefault();// using this page stop being refreshing
    var self = this;

    $.ajax({
        type: 'POST',
        url: '',
        data: $(this).serialize(),
        success: function () {
            $(self).closest("div").hide();
        }
    });

});

/* swap between the different display languages in the admin panel */
function swapOriginal(version) {
    var versions = ["kor", "tw", "hk"];
    for(var i = 0; i < versions.length; i++) {
        if(versions[i] == version) {
            $("." + versions[i] + "-text").addClass("active");
            $("." + versions[i] + "-link").addClass("active");
        } else {
            $("." + versions[i] + "-text").removeClass("active");
            $("." + versions[i] + "-link").removeClass("active");
        }
    }
}

$(document).ready(function() {
    /* show difference between new/old strings in admin panel */
    $("td.old-translation").each(function() {
        var current_translation = $(this).text().trim();
        if(current_translation != "") {
            var new_translation = $(this).parent().find("textarea").text().trim();
            $(this).html(diffString(current_translation, new_translation));
        }
    });
});