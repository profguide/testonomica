import $ from 'jquery';

$(() => {
    // Collect default values
    const VARIABLES = {
        'SUM': 'Набранная сумма всех числовых значений',
        'SCALE': 'Процент всех числовых значений от максимума',
        'NON_NEGATIVE_ANSWER_VALUES_SUM': 'Сумма всех неотрицательных значений (числа, строки)',
    };

    // Add variables from questions
    const grabQuestionsVars = () => {
        $("input[name*='[main][value]']").each(function () {
            const value = $(this).val();
            const text = $(this).parent().closest(".form-widget-compound").find("input[name*='[main][text]']").val();
            const textQuoted = "«" + text + "»"
            VARIABLES["REPEATS." + value + ".sum"] = "Ответ " + textQuoted + ": кол-во ответов";
            VARIABLES["REPEATS." + value + ".percentage"] = "Ответ " + textQuoted + ": процент кол-ва от общего числа ответов";
            VARIABLES["REPEATS." + value + ".percentage_value"] = "Ответ " + textQuoted + ": процент кол-ва от максимума ответа " + textQuoted;
        });
    }
    grabQuestionsVars();

    // Create the prototype select node
    const VARIABLES_SELECT_NODE = document.createElement('select');
    $.each(VARIABLES, function (value, name) {
        $(VARIABLES_SELECT_NODE).append($("<option>").attr('value', value).text(name));
    });

    //
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
    // 4. call this function on load
    // 5. call this function on add elements

    $("input[name*='[variableName]']").each(function () {
        replaceInputWithSelect(this);
    });
});