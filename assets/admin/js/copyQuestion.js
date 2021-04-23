import $ from 'jquery';

$(function () {
    function getId(question) {
        const regex = /Test_questions_([0-9]+)/;
        return parseInt(question.match(regex)[1]);
    }

    $('.field-collection-copy-button').on('click', function () {
        const block = $('#Test_questions');
        const questionSource = block.children().last();
        const questionSourceHtml = questionSource.html();

        // trigger clicking add btn
        $(this).siblings('.field-collection-add-button').trigger('click');
        const newQuestion = block.children().last();
        // console.log(newQuestion);

        const newId = getId(newQuestion.html());
        console.log(newId);
        const html = questionSourceHtml
            .replaceAll(/Test_questions_[0-9]+/g, 'Test_questions_' + newId)
            .replaceAll(/Test\[questions\]\[[0-9]+\]/g, 'Test[questions][' + newId + ']')
        // newQuestion.remove();
        newQuestion.html(html);
        // block.append(newQuestion.clone())
    });
});