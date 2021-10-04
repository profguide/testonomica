import React, {Component} from "react";
import Welcome from "./Welcome";
import Result from "./Result";
import ServiceApi from "../../service/ServiceApi";
import {STATUS_FINISHED, STATUS_IN_PROGRESS, STATUS_NONE} from "../../const";
import QuestionScreen from "../form/QuestionScreen";

/**
 * High-level API for website: handling clicks, showing the result
 * todo think of moving all question logic to QuestionScreen
 *  App will have only the questionsFinishedHandler, startClickHandler, restoreClickHandler etc, which
 *  operate with QuestionScreen, passing to it this.state.command
 *
 * что происходит после сохранения результата?
 * 1. отправить юзера по адресу /test/result/?id=key
 *  для тестономики
 *  не для интеграции
 * 2. выводить результат в компоненте Result
 *  для интеграции
 *  не для тестономики
 *
 * нужно дать возможность подписаться на событие сохранения результата, для того чтобы,
 *  кто надо мог сохранить себе id результата (и желательно текст результата, чтобы не тянуть лишний раз)
 *  + тестономика
 *  + профгид
 *  + все партнеры, кто хочет загрузить результат себе.
 *
 * текст результата сразу получать из /save? почему нет?
 *
 * Короче, можно подписаться на событие конец теста чтобы порекрыть дефолное поведение - поаз результата
 */
export default class App extends Component {
    constructor(props) {
        super(props);

        this.api = new ServiceApi({
            testId: props.testId,
            host: props.host,
            token: props.token
        });

        this.state = {
            isLoading: null, // for disabling buttons, showing loader
            error: null,
            test: null,
            question: null, // the current question
        };

        this.eventListeners = {};

        this.startClickHandler = this.startClickHandler.bind(this);
        this.restoreClickHandler = this.restoreClickHandler.bind(this);
        this.selectionHandler = this.selectionHandler.bind(this);
        this.goForwardHandler = this.goForwardHandler.bind(this);
        this.goBackHandler = this.goBackHandler.bind(this);
    }

    addEventListener(eventName, callback) {
        this.eventListeners[eventName] = callback;
        // console.log(this.eventListeners.finish);
    }

    trigger(eventName, event) {
        if (this.eventListeners[eventName]) {
            this.eventListeners[eventName](event);
        }
    }

    componentDidMount() {
        this.loadTest();
    }

    loadTest() {
        this.setState({...this.state, isLoading: true});
        this.wrapResponse(this.api.description(), (test) => {
            // if test was over, but result was not save for some reason
            if (this.api.progressFull() && this.api.status() === STATUS_IN_PROGRESS) {
                this.wrapResponse(this.api.saveResult(), (key) => {
                    this.trigger('finish', key);
                    this.setState({...this.state, isLoading: false, test})
                })
            } else {
                this.setState({...this.state, isLoading: false, test});
            }
        })
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

    saveResult() {
        this.setState({...this.state, isLoading: true})
        this.api.saveResult().then((key) => {
            this.trigger('finish', key);
            this.setState({...this.state, isLoading: false, question: null})
        });
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

    startClickHandler() {
        this.start();
    }

    restoreClickHandler() {
        this.next();
    }

    status() {
        return this.api.status();
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

    // The user made his choice
    selectionHandler(value) {
        // save the answer
        this.api.addAnswer(value);
        if (!this.api.progressFull()) {
            this.next(); // load next question
        } else {
            this.saveResult(); // save result
            // todo почему вопрос не исчезает? потому что вопрос есть. Да, надо выносить работу с вопросами в отдельный компонент
        }
    }

    render() {
        if (!this.state.test) {
            return 'Загрузка';
        }
        // todo think of wrapper in order to make it mute while isLoading
        let screen;
        if (this.state.question) {
            screen = <QuestionScreen testId={this.props.testId}
                                     question={this.state.question}
                                     questionAnsweredHandler={this.selectionHandler}
                                     isLoading={this.state.isLoading}
                                     goForwardHandler={this.goForwardHandler}
                                     goBackHandler={this.goBackHandler}/>
        } else if (this.status() === STATUS_NONE || this.status() === STATUS_IN_PROGRESS) {
            // Idea for the future: if (this.autoRestore) ... restore without Welcome screen.
            screen = <Welcome test={this.state.test}
                              status={this.status()}
                              startClickHandler={this.startClickHandler}
                              restoreClickHandler={this.restoreClickHandler}/>
        } else if (this.status() === STATUS_FINISHED) {
            screen = <Result api={this.api} restartClickHandler={this.startClickHandler}/>
        } else {
            throw new Error('Unknown status "' + this.status() + '".');
        }

        return (
            <div className={'tnc'}>
                {screen}
            </div>
        );
    }
}