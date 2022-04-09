$(document).on("click", '[data-request="ajax-submit"]', function() {
    /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
    $("#cover").show();
    $(".alert").remove();
    $(".has-error").removeClass("has-error");
    $(".help-block").remove();
    var $this = $(this);
    var $target = $this.data("target");
    var $url = $($target).attr("action");
    var $method = $($target).attr("method");
    var $modal = $this.data("modal");
    var $data = new FormData($($target)[0]);

    if (!$method) {
        $method = "get";
    }
    $.ajax({
        url: $url,
        data: $data,
        cache: false,
        type: $method,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function($response) {
            if ($response.status === true) {
                if ($response.redirect) {
                    if ($response.modal) {
                        if ($response.redirect != true) {
                            setTimeout(function() {
                                window.location.href = $response.redirect;
                            }, 10);
                        }
                        if ($response.redirect == true) {
                            window.location.reload(true);
                        }
                    } else {
                        if ($response.redirect != true) {
                            window.location.href = $response.redirect;
                        }
                        if ($response.redirect == true) {
                            window.location.reload(true);
                        }
                    }
                }
            } else {
                if (
                    $response.message.length > 0 &&
                    $response.message !== "M0000"
                ) {
                    $(".messages").html($response.message);
                }

                if (Object.size($response.data) > 0) {
                    show_validation_error($response.data);
                }
            }
            $("#cover").hide();
        },
    });
});

function show_validation_error(msg) {
    if ($.isPlainObject(msg)) {
        $data = msg;
    } else {
        $data = $.parseJSON(msg);
    }
    $.each($data, function(index, value) {
        var name = index.replace(/\./g, "][");
        if (index.indexOf(".") !== -1) {
            name = name + "]";
            name = name.replace("]", "");
        }
        if (name.indexOf("[]") !== -1) {
            $('form [name="' + name + '"]')
                .last()
                .closest("")
                .addClass("has-error");
            $('form [name="' + name + '"]')
                .last()
                .closest(".form-group")
                .find("")
                .append(
                    '<span class="help-block text-danger">' + value + "</span>"
                );
        } else if ($('form [name="' + name + '[]"]').length > 0) {
            $('form [name="' + name + '[]"]')
                .closest(".form-group")
                .addClass("has-error");
            $('form [name="' + name + '[]"]')
                .parent()
                .after(
                    '<span class="help-block text-danger">' + value + "</span>"
                );
        } else {
            if (
                $('form [name="' + name + '"]').attr("type") == "checkbox" ||
                $('form [name="' + name + '"]').attr("type") == "radio"
            ) {
                if (
                    $('form [name="' + name + '"]').attr("type") == "checkbox"
                ) {
                    $('form [name="' + name + '"]')
                        .closest(".form-group")
                        .addClass("has-error");
                    $('form [name="' + name + '"]')
                        .parent()
                        .after(
                            '<span class="help-block text-danger">' +
                            value +
                            "</span>"
                        );
                } else {
                    $('form [name="' + name + '"]')
                        .closest(".form-group")
                        .addClass("has-error");
                    $('form [name="' + name + '"]')
                        .parent()
                        .parent()
                        .append(
                            '<span class="help-block text-danger">' +
                            value +
                            "</span>"
                        );
                }
            } else if ($('form [name="' + name + '"]').get(0)) {
                if (
                    $('form [name="' + name + '"]').get(0).tagName == "select"
                ) {
                    $('form [name="' + name + '"]')
                        .closest(".form-group")
                        .addClass("has-error");
                    $('form [name="' + name + '"]')
                        .parent()
                        .after(
                            '<span class="help-block text-danger">' +
                            value +
                            "</span>"
                        );
                } else if (
                    $('form [name="' + name + '"]').attr("type") ==
                    "password" &&
                    $('form [name="' + name + '"]').hasClass(
                        "hideShowPassword-field"
                    )
                ) {
                    $('form [name="' + name + '"]')
                        .closest(".form-group")
                        .addClass("has-error");
                    $('form [name="' + name + '"]')
                        .parent()
                        .after(
                            '<span class="help-block text-danger">' +
                            value +
                            "</span>"
                        );
                } else {
                    $('form [name="' + name + '"]')
                        .closest(".form-group")
                        .addClass("has-error");
                    $('form [name="' + name + '"]').after(
                        '<span class="help-block text-danger">' +
                        value +
                        "</span>"
                    );
                }
            } else {
                $('form [name="' + name + '"]')
                    .closest(".form-group")
                    .addClass("has-error");
                $('form [name="' + name + '"]').after(
                    '<span class="help-block text-danger">' + value + "</span>"
                );
            }
        }
    });
    scroll();
}

function scroll() {
    if ($(".help-block").not(".modal .help-block").length > 0) {
        $("html, body").animate({
                scrollTop: $(".help-block").offset().top - 100,
            },
            200
        );
    }
}

Object.size = function(obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};