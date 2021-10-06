import React, {Component} from "react";
import {
    QUESTION_TYPE_CHECKBOX,
    QUESTION_TYPE_OPTION,
    QUESTION_TYPE_RATING,
    QUESTION_TYPE_TEXT,
    QUIZ_TASK_RESTORE
} from "../../const";
import FormOption from "../form/FormOption";
import FormCheckbox from "../form/FormCheckbox";
import FormText from "../form/FormText";
import FormRating from "../form/FormRating";
import ProgressBar from "../form/ProgressBar";
import Loading from "../form/Loading";

export default class QuizScreen extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: true,
            error: null,
            question: null,
        }

        this.api = props.api;

        this.selectionHandler = this.selectionHandler.bind(this);
        this.goForwardHandler = this.goForwardHandler.bind(this);
        this.goBackHandler = this.goBackHandler.bind(this);
    }

    componentDidMount() {
        if (this.props.task === QUIZ_TASK_RESTORE) {
            this.next();
        } else {
            this.start();
        }
    }

    // The user made his choice
    selectionHandler(value) {
        // save the answer
        this.api.addAnswer(value);
        if (!this.api.progressFull()) {
            this.next();
        } else {
            this.props.questionsOverHandler();
        }
    }

    start() {
        this.setState({...this.state, isLoading: true});
        this.api.clear();
        this.wrapQuestionResponse(this.api.first());
    }

    next() {
        this.setState({...this.state, isLoading: true});
        this.wrapQuestionResponse(this.api.next());
    }

    prev() {
        this.setState({...this.state, isLoading: true});
        this.wrapQuestionResponse(this.api.prev());
    }

    // Forward button clicked: save answer and load the next question
    goForwardHandler() {
        this.api.addAnswer(null);
        this.next();
    }

    // Back button clicked: load the previous question
    goBackHandler() {
        this.prev();
    }

    wrapQuestionResponse(promise) {
        this.wrapResponse(promise, (question) => {
            this.setState({...this.state, isLoading: false, question: question})
        });
    }

    wrapResponse(promise, callback) {
        promise.then(callback).catch(error => {
            console.error(error);
            this.setState({...this.state, isLoading: false, error: 'Произошла ошибка во время загрузки.'});
        });
    }

    render() {
        if (this.state.question === null) {
            return <Loading/>;
        }
        const question = this.state.question;
        const options = question.options;
        const type = question.type.get();
        const enabledForward = question.enabledForward && question.number < question.length;
        const enabledBack = question.enabledBack && question.number > 1;
        let form;

        if (type === QUESTION_TYPE_OPTION) {
            form = <FormOption key={question.id}
                               options={options}
                               isLoading={this.state.isLoading}
                               enabledBack={enabledBack}
                               enabledForward={enabledForward}
                               selectionHandler={this.selectionHandler}
                               goForwardHandler={this.goForwardHandler}
                               goBackHandler={this.goBackHandler}/>
        } else if (type === QUESTION_TYPE_CHECKBOX) {
            form = <FormCheckbox key={question.id}
                                 options={options}
                                 isLoading={this.state.isLoading}
                                 count={question.count}
                                 enabledBack={enabledBack}
                                 enabledForward={enabledForward}
                                 selectionHandler={this.selectionHandler}
                                 goForwardHandler={this.goForwardHandler}
                                 goBackHandler={this.goBackHandler}/>
        } else if (type === QUESTION_TYPE_TEXT) {
            form = <FormText key={question.id}
                             options={options}
                             isLoading={this.state.isLoading}
                             count={question.count}
                             enabledBack={enabledBack}
                             enabledForward={enabledForward}
                             selectionHandler={this.selectionHandler}
                             goForwardHandler={this.goForwardHandler}
                             goBackHandler={this.goBackHandler}/>
        } else if (type === QUESTION_TYPE_RATING) {
            form = <FormRating key={question.id}
                               options={options}
                               isLoading={this.state.isLoading}
                               count={question.count}
                               enabledBack={enabledBack}
                               enabledForward={enabledForward}
                               selectionHandler={this.selectionHandler}
                               goForwardHandler={this.goForwardHandler}
                               goBackHandler={this.goBackHandler}/>
        }

        return (
            <article className={'tnc-q tnc-q__' + this.props.testId + '-' + question.id}>
                {question.img
                    ? <img className={'tnc-q__img'} src={question.img} alt={'Задание'}/>
                    : null}
                <h2 className={'tnc-q__name'}>{question.name}</h2>
                {question.text
                    ? <div className={'tnc-q__text'} dangerouslySetInnerHTML={{__html: question.text}}/>
                    : null}
                {form}
                <ProgressBar length={question.length} number={question.number}/>
            </article>
        );
    }
}