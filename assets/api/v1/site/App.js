import {QUIZ_TASK_RESTORE, QUIZ_TASK_START, STATUS_FINISHED, STATUS_IN_PROGRESS} from "../const";

import React, {Component} from "react";
import ServiceApi from "../service/ServiceApi";
import WelcomeScreen from "./screen/WelcomeScreen";
import ResultScreen from "./screen/ResultScreen";
import QuizScreen from "./screen/QuizScreen";

const SCREEN_WELCOME = 'welcome';
const SCREEN_QUIZ = 'quiz';
const SCREEN_RESULT = 'result';

export const TNC_EVENT_FINISH = 'finish';

/**
 * High-level API for website: handling clicks, showing the result
 * todo продумать
 * 1. концовка чтобы на тестономике могла быть широкоформатной.
 *    может быть это можно сделать за счёт стилей?
 *    Да, стили могут решить проблему. Допустим по умолчанию тест может быть узкий.
 *    А на тестономике tnc-result - 100%, а tnc-result__text - как container.
 * 2. концовка на скилбоксе может не иметь урл постоянной ссылки и соц. кнопки.
 *    либо стилями убирать, либо настраивать темплейт, либо для скилбокса делать отдельное приложение.
 * 3. если какие-то сайты будут жаловаться, что стили кривые - нужно продумать вариант с iframe.
 *
 *
 */
export default class App extends Component {
    constructor(props) {
        super(props);

        // todo props.dispatcher;
        this.dispatcher = props.dispatcher;

        this.api = new ServiceApi({
            testId: props.testId,
            host: props.host,
            token: props.token
        });

        this.state = {
            isLoading: null, // for disabling buttons, showing loader
            error: null,
            test: null,
            screen: SCREEN_WELCOME,
            quiz_task: QUIZ_TASK_START
        };

        this.startClickHandler = this.startClickHandler.bind(this);
        this.restoreClickHandler = this.restoreClickHandler.bind(this);
        this.questionsOverHandler = this.questionsOverHandler.bind(this);
    }

    componentDidMount() {
        this.loadTest();
    }

    trigger(e) {
        if (this.dispatcher) {
            this.dispatcher.dispatchEvent(e);
        }
    }

    status() {
        return this.api.status();
    }

    loadTest() {
        this.setState({...this.state, isLoading: true});
        this.wrapResponse(this.api.description(), (test) => {
            // if test was over, but result was not save for some reason
            if (this.api.progressFull() && this.api.status() === STATUS_IN_PROGRESS) {
                this.wrapResponse(this.api.saveResult(), (key) => {
                    this.trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
                    // show result
                    this.setState({...this.state, isLoading: false, test, screen: SCREEN_RESULT})
                })
            } else if (this.status() === STATUS_FINISHED) {
                // show result
                this.setState({...this.state, isLoading: false, test, screen: SCREEN_RESULT});
            } else {
                // show quiz
                this.setState({...this.state, isLoading: false, test});
            }
        })
    }

    restoreClickHandler() {
        this.setState({...this.state, screen: SCREEN_QUIZ, quiz_task: QUIZ_TASK_RESTORE})
    }

    startClickHandler() {
        this.setState({...this.state, screen: SCREEN_QUIZ, quiz_task: QUIZ_TASK_START})
    }

    /**
     * Question over: save result
     */
    questionsOverHandler() {
        this.setState({...this.state, isLoading: true})
        this.wrapResponse(this.api.saveResult(), (key) => {
            this.trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
            this.setState({...this.state, isLoading: false, screen: SCREEN_RESULT})
        });
    }

    wrapResponse(promise, callback) {
        promise.then(callback).catch(error => {
            console.error(error);
            this.setState({...this.state, isLoading: false, error: 'Произошла ошибка во время загрузки.'});
        });
    }

    render() {
        if (!this.state.test) {
            return null;
        }
        let screen;
        if (this.state.screen === SCREEN_QUIZ) {
            screen = <QuizScreen testId={this.props.testId} api={this.api}
                                          questionsOverHandler={this.questionsOverHandler}
                                          task={this.state.quiz_task}/>
        } else if (this.state.screen === SCREEN_RESULT) {
            screen = <ResultScreen api={this.api} restartClickHandler={this.startClickHandler}/>
        } else {
            screen = <WelcomeScreen test={this.state.test}
                                    status={this.status()}
                                    startClickHandler={this.startClickHandler}
                                    restoreClickHandler={this.restoreClickHandler}/>
        }

        return (
            <div className={'tnc'}>
                {screen}
            </div>
        );
    }
}