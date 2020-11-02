// var requestOptions = {
//     crossDomain: true,
//     isSameSite: 'None',
//     secure: true,
//     xhrFields: {
//         withCredentials: true
//     }
// };
import $ from "jquery";

window.testonomica = (function () {
    return {
        'nextUrl': function () {
            return "/tests/api/";
        },
        'onFinish': function (uuid) {
            console.log(uuid);
            window.location.replace(window.testonomica.resultUrl(uuid));
        },
        'resultUrl': function (uuid) {
            return "/test/r/" + uuid + "/";
        }
    }
})();

$(function () {
    const BODY = $("body");
    const PREVIEW_SCREEN = $("#test-preview-screen");
    const PREVIEW_FORM = $("#test__start-form");
    const AJAX_SCREEN = $("#test__ajax-screen");
    const QUESTION_FORM_SELECTOR = ".test__question-form";
    const BTN_START = $(".start-test");
    const BTN_RESTORE = $(".restore-test");
    const BTN_BACK_SELECTOR = "#test-question-back-btn";
    const BTN_NEXT_SELECTOR = "#test-question-next-btn";
    const BTN_AGAIN_SELECTOR = ".test__btn-again";
    const TEST_OPTIONS_HOLDER_SELECTOR = ".test__options_holder";
    const TEST_OPTION_SELECTOR = ".test__option";
    let AUTO_ENABLED = false;

    const toast = function (message, type) {
        let bg = "primary";
        if (type !== undefined) {
            bg = type;
        }
        $("#toast-place").append('<div class="toast fade show" data-delay="2000"><div class="toast-body bg-' + bg + ' text-white">' + message + ' <span aria-hidden="true">&times;</span></div></div>');
        $(".toast").toast({
            delay: 5000,
            animation: true
        });
    };
    $("#toast-place").on("click", ".toast", function (t) {
        $(this).toast("hide");
    });

    BTN_START.on("click", function (t) {
        console.log("click start btn()");
        start();
        return false
    });

    BTN_RESTORE.on("click", function (t) {
        restore();
        return false;
    });

    BODY.on("click", BTN_BACK_SELECTOR, function (t) {
        back();
        return false;
    });

    BODY.on("click", BTN_NEXT_SELECTOR, function (t) {
        next(questionFormSerialize());
        return false;
    });

    BODY.on("click", BTN_AGAIN_SELECTOR, function (t) {
        start();
        return false;
    });

    BODY.on("change", QUESTION_FORM_SELECTOR + " input[name=\"answer\"]", function (e) {
        if ($(this).data("method") === "RATING") {
            onChangeRatingHandler(this);
        } else if ($(this).data("method") === "CHECKBOX") {
            onChangeCheckBoxHandler(this);
        } else if ($(this).data("method") === "TEXT") {
            onChangeTextHandler();
        } else {
            onChangeRadioHandler(e, this);
        }
        return false;
    });

    const questionFormSerialize = function () {
        let formData = BODY.find(QUESTION_FORM_SELECTOR).serialize().replace(/&answer=(.*[^&])?/g, "");
        let i;
        for (i = 0; i < answers.length; i++) {
            formData += "&answer=" + answers[i];
        }
        if (i === 0) {
            formData += "&answer=";
        }
        return formData;
    };

    const previewFormSerialize = function () {
        return PREVIEW_FORM.serialize();
    };

    const onChangeRatingHandler = function (input) {
        answers.push($(input).val());
        const left = $(input).parents(TEST_OPTIONS_HOLDER_SELECTOR).find(TEST_OPTION_SELECTOR).length;
        const optionBlock = $(input).parent(TEST_OPTION_SELECTOR);
        optionBlock.fadeOut(200).promise().done(function () {
            $(optionBlock).remove();
            if (AUTO_ENABLED) {
                auto();
            }
        });
        if (left === 1) {
            $(TEST_OPTION_SELECTOR).remove();
            next(questionFormSerialize());
        }
    };

    const onChangeCheckBoxHandler = function (input) {
        const index = answers.indexOf($(input).val());
        enableNextBtn();
        if (index > -1) {
            $(input).prop("checked", false);
            answers.splice(index, 1);
        } else {
            $(input).attr('checked', 'checked');
            answers.push($(input).val());
        }
        const inputs = $(QUESTION_FORM_SELECTOR).find("input[type=\"checkbox\"]");
        if (answers.length >= parseInt($(input).data("limit"))) {
            const checkedInputs = inputs.not(":checked");
            checkedInputs.attr("disabled", "disabled");
            checkedInputs.parent().addClass("disabled");
        } else {
            inputs.removeAttr("disabled");
            inputs.parent().removeClass("disabled");
        }
    };

    const onChangeRadioHandler = function (e, input) {
        answers = [$(input).val()];
        if (showAnswer) {
            enableNextBtn();
            $(".test__option").each(function (i, o) {
                const _input = $(o).children("input");
                if (_input[0] === input) {
                    $(o).addClass("test__option_chosen");
                    if ($(input).data("is-correct") === true) {
                        $(o).addClass("test__option_correct");
                        if (textOnRight != null) {
                            $(o).find(".test__option-reveal").html(textOnRight).show();
                        }
                    } else {
                        $(o).addClass("test__option_wrong");
                        if (textOnWrong != null) {
                            $(o).find(".test__option-reveal").html(textOnWrong).show();
                        }
                    }
                } else if (_input.data("is-correct") === true) {
                    $(o).addClass("test__option_correct");
                } else {
                    // just show statistics
                }
                _input.prop("disabled", true);
            });
        } else {
            next(questionFormSerialize())
        }
    };

    const onChangeTextHandler = function () {
        answers = [];
        const _inputs = $(QUESTION_FORM_SELECTOR + " input[name=\"answer\"]");
        _inputs.each(function (i, o) {
            answers.push($(o).val());
        });
    };

    const API = function () {
        // console.log(missed_var_to_check_google_analytics_error_report)
        this.next = function (data, success, fail) {
            $.ajax(window.testonomica.nextUrl(), {
                'data': data,
                'method': 'POST'
            }).done(function (data, status, xhr) {
                success(data, xhr);
            }).fail(function (jqXHR) {
                console.log("error", jqXHR);
                fail();
            });
        };
    };
    const api = new API();

    function enableNextBtn() {
        $(BTN_NEXT_SELECTOR).addClass("test__btn-direct_forced");
    }

    function hideAllScreens(callback) {
        $("#test-screens-wrapper > *").hide().promise().done(callback);
    }

    function renderHtml(html) {
        if (AJAX_SCREEN.is(":hidden")) {
            AJAX_SCREEN.html(html).fadeIn();
        } else {
            AJAX_SCREEN.hide().promise().done(function () {
                AJAX_SCREEN.html(html);
                if (AUTO_ENABLED) {
                    AJAX_SCREEN.show();
                    auto();
                } else {
                    AJAX_SCREEN.fadeIn();
                }
            });
        }
    }

    function start() {
        hideAllScreens(function () {
            next(previewFormSerialize() + "&clear=1");
        });
    }

    function next(data) {
        api.next(data, function (data, xhr) {
            renderHtml(data);
            // console.log(xhr.getResponseHeader('test-status'))
            if (xhr.getResponseHeader('result-uuid')) {
                window.testonomica.onFinish(xhr.getResponseHeader('result-uuid'))
            }
        }, function () {
            toast("Произошла ошибка", "danger");
        });
    }

    function restore() {
        hideAllScreens(function () {
            // console.log(previewFormSerialize());
            next(previewFormSerialize() + "&restore=1");
        });
    }

    function back() {
        next(questionFormSerialize() + "&back=1");
    }

    window.auto = function () {
        AUTO_ENABLED = true;
        const answers = $("body").find(QUESTION_FORM_SELECTOR + " input[name=\"answer\"]");
        if ($(answers[0]).prop("type") === "text") {
            answers.each(function (index, node) {
                $(node).val(42);
            });
            $("#test-question-next-btn").click();
        } else if ($(answers[0]).prop("type") === "radio") {
            const randI = getRandomInt(answers.length);
            $(answers[randI]).click();
        }
    };

    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }
});