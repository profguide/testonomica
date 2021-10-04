import {QUESTION_TYPE_CHECKBOX, QUESTION_TYPE_OPTION, QUESTION_TYPE_RATING, QUESTION_TYPE_TEXT} from "../../const";

export default class QuestionType {
    constructor(value) {
        // assert...
        if (![
            QUESTION_TYPE_OPTION,
            QUESTION_TYPE_CHECKBOX,
            QUESTION_TYPE_TEXT,
            QUESTION_TYPE_RATING
        ].includes(value)) {
            throw new Error('Unknown question type: ' + value);
        }
        this.value = value;
    }

    get() {
        return this.value;
    }
}