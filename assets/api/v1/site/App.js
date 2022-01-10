import React, {useEffect, useState} from "react";
import {QUIZ_TASK_RESTORE, QUIZ_TASK_START, STATUS_FINISHED, STATUS_IN_PROGRESS} from "../const";
import ResultScreen from "./screen/ResultScreen";
import WelcomeScreen from "./screen/WelcomeScreen";
import QuizScreen from "./screen/QuizScreen";
import PaymentScreen from "./screen/PaymentScreen";

const SCREEN_WELCOME = 'welcome';
const SCREEN_QUIZ = 'quiz';
const SCREEN_RESULT = 'result';
const SCREEN_PAYMENT = 'payment';

export const TNC_EVENT_LOADED = 'loaded';
export const TNC_EVENT_RESIZE = 'resize';
export const TNC_EVENT_FINISH = 'finish';

export default (props) => {
    const api = props.api;
    const [state, changeState] = useState({
        isLoading: true,
        error: null,
        test: null,
        screen: null,
        quizTask: null,
        mainDivRef: null,
        observer: new ResizeObserver((e) => {
            trigger(new CustomEvent(TNC_EVENT_RESIZE, {detail: e}));
        }),
    });

    const setRef = (ref) => {
        const {observer} = state;
        state.mainDivRef = ref;
        if (state.mainDivRef) {
            observer.observe(state.mainDivRef)
        }
    }

    const trigger = (e) => {
        if (props.dispatcher) {
            props.dispatcher.dispatchEvent(e);
        }
    }

    const wrapRequest = (promise, callback) => {
        promise.then(callback).catch(error => {
            if (error.response.status === 402) {
                console.info('Payment required');
                // error.response.headers['x-token']
                onPaymentRequired();
                // trigger(new CustomEvent(TNC_EVENT_LOADED));
            } else {
                changeState({...state, isLoading: false, error: 'Произошла ошибка во время загрузки теста.'});
                console.error(error);
            }
        });
    }

    const onPaymentRequired = () => {
        changeState({...state, screen: SCREEN_PAYMENT, isLoading: false})
    }

    const whenClickRestore = () => {
        changeState({...state, screen: SCREEN_QUIZ, quizTask: QUIZ_TASK_RESTORE});
    }

    const whenClickStart = () => {
        changeState({...state, screen: SCREEN_QUIZ, quizTask: QUIZ_TASK_START});
    }

    const whenQuestionsOver = () => {
        changeState({...state, isLoading: true});
        wrapRequest(api.saveResult(), (key) => {
            trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
            changeState({...state, isLoading: false, screen: SCREEN_RESULT});
        });
    }

    useEffect(() => {
        // load the test and check progress
        wrapRequest(api.description(), (test) => {
            // if test was over, but result was not save for some reason
            if (api.progressFull() && api.status() === STATUS_IN_PROGRESS) {
                // show save result, load and show the conclusion.
                wrapRequest(api.saveResult(), (key) => {
                    trigger(new CustomEvent(TNC_EVENT_LOADED));
                    trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
                    changeState({...state, isLoading: false, test: test, screen: SCREEN_RESULT});
                })
            } else if (api.status() === STATUS_FINISHED) {
                trigger(new CustomEvent(TNC_EVENT_LOADED));
                changeState({...state, isLoading: false, test: test, screen: SCREEN_RESULT});
            } else {
                trigger(new CustomEvent(TNC_EVENT_LOADED));
                changeState({...state, isLoading: false, test: test, screen: SCREEN_WELCOME});
            }
        });
    }, []) // ex ComponentDidMount

    if (state.isLoading) {
        return null;
    }
    if (state.error) {
        return state.error;
    }

    if (state.screen === SCREEN_PAYMENT) {
        return <PaymentScreen api={api}/>;
    }

    if (!state.test) {
        return null;
    }

    return (
        <div id={'tnc'} className={'tnc'} ref={setRef}>
            <div className={'container'}>
                {state.screen === SCREEN_RESULT ?
                    <ResultScreen api={api} test={state.test} restartClickHandler={whenClickStart}/> : null
                }
                {state.screen === SCREEN_WELCOME ?
                    <WelcomeScreen test={state.test}
                                   status={api.status()}
                                   startClickHandler={whenClickStart}
                                   restoreClickHandler={whenClickRestore}/> : null
                }
                {state.screen === SCREEN_QUIZ ?
                    <QuizScreen testId={api.testId}
                                api={api}
                                questionsOverHandler={whenQuestionsOver}
                                task={state.quizTask}/> : null
                }
            </div>
        </div>
    )
}