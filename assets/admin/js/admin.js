import $ from 'jquery';

// Переменные калькулятора
$(() => {
    // Default
    const VARIABLES = {
        'SUM': 'Набранная сумма всех числовых значений',
        'SCALE': 'Процент всех числовых значений от максимума',
        'NON_NEGATIVE_ANSWER_VALUES_SUM': 'Сумма всех неотрицательных значений (числа, строки)',
    };

    // Analysis questions and pushing variables
    const grabQuestionsVars = () => {
        $("input[name*='[main][value]']").each(function () {
            const value = $(this).val();
            // const text = $(this).parent().closest(".form-widget-compound").find("input[name*='[main][text]']").val();
            const quotedValue = "«" + value + "»"
            VARIABLES["REPEATS." + value + ".sum"] = "Ответ " + quotedValue + ": кол-во ответов";
            VARIABLES["REPEATS." + value + ".percentage"] = "Ответ " + quotedValue + ": процент кол-ва от общего числа ответов";
            VARIABLES["REPEATS." + value + ".percentage_value"] = "Ответ " + quotedValue + ": процент кол-ва от максимума ответа " + quotedValue;
        });
    }
    grabQuestionsVars();

    // Create the prototype select node
    const VARIABLES_SELECT_NODE = document.createElement('select');
    $.each(VARIABLES, function (value, name) {
        $(VARIABLES_SELECT_NODE).append($("<option>").attr('value', value).text(name));
    });

    // replaces input element with a new select element
    const replaceInputWithSelect = (input) => {
        const parent = $(input).parent();
        // склонируем select
        const select = VARIABLES_SELECT_NODE.cloneNode();
        // установим атрибуты, как у инпута
        $(select).addClass(input.className)
        $(select).attr('name', $(input).attr('name'));
        $(select).attr('id', $(input).attr('id'));
        // скопируем все опшны
        $(select).html($(VARIABLES_SELECT_NODE).html());
        // выбор текущего значения
        $(select).children("option[value='" + $(input).val() + "']").prop('selected', true);
        // удалим инпут
        $(input).remove();
        // добавим селект
        $(parent).append(select);
    }

    // onload find all inputs with variables and replace them with a select
    $("input[name*='[variableName]']").each(function () {
        replaceInputWithSelect(this);
    });
});

// показ/скрытие блока с правильными и неправильными ответами по включенной галочке
$(() => {
    const PARENT_WIDGET = ".question-widget";
    const CHECKBOX = '.question-checkbox-show-answer';
    const BLOCK = '.question-show-answer-block';
    const toggle = function (input) {
        if ($(input).is(':checked')) {
            $(input).closest(PARENT_WIDGET).find(BLOCK).show();
        } else {
            $(input).closest(PARENT_WIDGET).find(BLOCK).hide();
        }
    }
    $('body').on('click', CHECKBOX, function () {
        toggle(this);
    });
    $(CHECKBOX).each(function () {
        toggle(this);
    });
});

// .option-field
// элементы формы скрыты если в них нет значений
// они показываются при нажатии на заголовок
$(() => {
    $('body').on('click', '.optional-field > label, .optional-field > legend', function () {
        $(this).parent().find('.form-widget:first').toggle();
    });
    $(".optional-field > .form-widget").each(function () {
        let empty = true;
        $(this).find('.form-control').each((i, o) => {
            if ($(o).val().length > 0) {
                empty = false;
            }
        });
        if (empty) {
            $(this).hide();
        }
    });
});