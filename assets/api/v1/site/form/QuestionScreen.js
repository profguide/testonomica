import React, {Component} from "react";
import {QUESTION_TYPE_CHECKBOX, QUESTION_TYPE_OPTION, QUESTION_TYPE_RATING, QUESTION_TYPE_TEXT} from "../../const";
import FormOption from "./FormOption";
import FormCheckbox from "./FormCheckbox";
import FormText from "./FormText";
import FormRating from "./FormRating";
import ProgressBar from "./ProgressBar";

export default class QuestionScreen extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const question = this.props.question;
        const options = question.options;
        const type = question.type.get();
        const enabledForward = question.enabledForward && question.number < question.length;
        const enabledBack = question.enabledBack && question.number > 1;
        let form;
        if (type === QUESTION_TYPE_OPTION) {
            form = <FormOption key={question.id}
                               options={options}
                               isLoading={this.props.isLoading}
                               enabledBack={enabledBack}
                               enabledForward={enabledForward}
                               questionAnsweredHandler={this.props.questionAnsweredHandler}
                               goForwardHandler={this.props.goForwardHandler}
                               goBackHandler={this.props.goBackHandler}/>
        } else if (type === QUESTION_TYPE_CHECKBOX) {
            form = <FormCheckbox key={question.id}
                                 options={options}
                                 isLoading={this.props.isLoading}
                                 count={question.count}
                                 enabledBack={enabledBack}
                                 enabledForward={enabledForward}
                                 questionAnsweredHandler={this.props.questionAnsweredHandler}
                                 goForwardHandler={this.props.goForwardHandler}
                                 goBackHandler={this.props.goBackHandler}/>
        } else if (type === QUESTION_TYPE_TEXT) {
            form = <FormText key={question.id}
                             options={options}
                             isLoading={this.props.isLoading}
                             count={question.count}
                             enabledBack={enabledBack}
                             enabledForward={enabledForward}
                             questionAnsweredHandler={this.props.questionAnsweredHandler}
                             goForwardHandler={this.props.goForwardHandler}
                             goBackHandler={this.props.goBackHandler}/>
        } else if (type === QUESTION_TYPE_RATING) {
            form = <FormRating key={question.id}
                               options={options}
                               isLoading={this.props.isLoading}
                               count={question.count}
                               enabledBack={enabledBack}
                               enabledForward={enabledForward}
                               questionAnsweredHandler={this.props.questionAnsweredHandler}
                               goForwardHandler={this.props.goForwardHandler}
                               goBackHandler={this.props.goBackHandler}/>
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