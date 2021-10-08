import React, {useEffect, useState} from "react";
import {QUIZ_TASK_RESTORE, QUIZ_TASK_START, STATUS_FINISHED, STATUS_IN_PROGRESS} from "../const";
import ResultScreen from "./screen/ResultScreen";
import WelcomeScreen from "./screen/WelcomeScreen";
import QuizScreen from "./screen/QuizScreen";

const SCREEN_WELCOME = 'welcome';
const SCREEN_QUIZ = 'quiz';
const SCREEN_RESULT = 'result';

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
            changeState({...state, isLoading: false, error: 'Произошла ошибка во время загрузки теста.'});
            console.error(error);
        });
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
    if (!state.test) {
        return null;
    }

    return (
        <div id={'tnc'} className={'tnc'} ref={setRef}>
            <div className={'container'}>
                {state.screen === SCREEN_RESULT ?
                    <ResultScreen api={api} test={state.test} restartClickHandler={whenClickStart}/>
                    : null
                }
                {state.screen === SCREEN_WELCOME ?
                    <WelcomeScreen test={state.test}
                                   status={api.status()}
                                   startClickHandler={whenClickStart}
                                   restoreClickHandler={whenClickRestore}/>
                    : null
                }
                {state.screen === SCREEN_QUIZ ?
                    <QuizScreen testId={api.testId}
                                api={api}
                                questionsOverHandler={whenQuestionsOver}
                                task={state.quizTask}/>
                    : null
                }
            </div>
        </div>
    )
}

// class App extends Component {
//     constructor(props) {
//         super(props);
//
//         // todo props.dispatcher;
//         this.dispatcher = props.dispatcher;
//
//         this.api = new ServiceApi({
//             testId: props.testId,
//             host: props.host,
//             token: props.token
//         });
//
//         this.state = {
//             isLoading: null, // for disabling buttons, showing loader
//             error: null,
//             test: null,
//             screen: SCREEN_WELCOME,
//             quiz_task: QUIZ_TASK_START
//         };
//
//         this.startClickHandler = this.startClickHandler.bind(this);
//         this.restoreClickHandler = this.restoreClickHandler.bind(this);
//         this.questionsOverHandler = this.questionsOverHandler.bind(this);
//     }
//
//     resizeIframe() {
//         window.parent.postMessage({frameHeight: 900}, this.props.host);
//     }
//
//     componentDidMount() {
//         this.loadTest();
//     }
//
//     trigger(e) {
//         if (this.dispatcher) {
//             this.dispatcher.dispatchEvent(e);
//         }
//     }
//
//     status() {
//         return this.api.status();
//     }
//
//     loadTest() {
//         this.setState({...this.state, isLoading: true});
//         this.wrapResponse(this.api.description(), (test) => {
//             // if test was over, but result was not save for some reason
//             if (this.api.progressFull() && this.api.status() === STATUS_IN_PROGRESS) {
//                 this.wrapResponse(this.api.saveResult(), (key) => {
//                     this.trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
//                     // show result
//                     this.trigger(new CustomEvent(TNC_EVENT_LOADED));
//                     this.setState({...this.state, isLoading: false, test, screen: SCREEN_RESULT})
//                 })
//             } else if (this.status() === STATUS_FINISHED) {
//                 // show result
//                 this.trigger(new CustomEvent(TNC_EVENT_LOADED));
//                 this.setState({...this.state, isLoading: false, test, screen: SCREEN_RESULT});
//             } else {
//                 // show quiz
//                 this.trigger(new CustomEvent(TNC_EVENT_LOADED));
//                 this.setState({...this.state, isLoading: false, test});
//             }
//         })
//     }
//
//     restoreClickHandler() {
//         this.setState({...this.state, screen: SCREEN_QUIZ, quiz_task: QUIZ_TASK_RESTORE})
//     }
//
//     startClickHandler() {
//         this.setState({...this.state, screen: SCREEN_QUIZ, quiz_task: QUIZ_TASK_START})
//     }
//
//     /**
//      * Question over: save result
//      */
//     questionsOverHandler() {
//         this.setState({...this.state, isLoading: true})
//         this.wrapResponse(this.api.saveResult(), (key) => {
//             this.trigger(new CustomEvent(TNC_EVENT_FINISH, {detail: {key}}));
//             this.setState({...this.state, isLoading: false, screen: SCREEN_RESULT})
//         });
//     }
//
//     wrapResponse(promise, callback) {
//         promise.then(callback).catch(error => {
//             console.error(error);
//             this.setState({...this.state, isLoading: false, error: 'Произошла ошибка во время загрузки.'});
//         });
//     }
//
//     render() {
//         if (!this.state.test) {
//             return null;
//         }
//
//         return (
//             <div id={'tnc'} className={'tnc'}>
//                 <div className={'container'}>
//
//                     {this.state.screen === SCREEN_RESULT ?
//                         <ResultScreen api={this.api} restartClickHandler={this.startClickHandler}/>
//                         : null
//                     }
//                     {this.state.screen === SCREEN_WELCOME ?
//                         <WelcomeScreen test={this.state.test}
//                                        status={this.status()}
//                                        startClickHandler={this.startClickHandler}
//                                        restoreClickHandler={this.restoreClickHandler}/>
//                         : null
//                     }
//                     {this.state.screen === SCREEN_QUIZ ?
//                         <QuizScreen testId={this.props.testId}
//                                     api={this.api}
//                                     questionsOverHandler={this.questionsOverHandler}
//                                     task={this.state.quiz_task}/>
//                         : null
//                     }
//
//                     {/*<CSSTransition in={this.state.screen === SCREEN_WELCOME} timeout={500} classNames="my-node" unmountOnExit>*/}
//                     {/*    <WelcomeScreen test={this.state.test}*/}
//                     {/*                   status={this.status()}*/}
//                     {/*                   startClickHandler={this.startClickHandler}*/}
//                     {/*                   restoreClickHandler={this.restoreClickHandler}/>*/}
//                     {/*</CSSTransition>*/}
//
//                     {/*<CSSTransition in={this.state.screen === SCREEN_RESULT} timeout={500} classNames="my-node" unmountOnExit>*/}
//                     {/*    <ResultScreen api={this.api} restartClickHandler={this.startClickHandler}/>*/}
//                     {/*</CSSTransition>*/}
//
//                     {/*<CSSTransition in={this.state.screen === SCREEN_QUIZ} timeout={500} classNames="my-node" unmountOnExit>*/}
//                     {/*    <QuizScreen testId={this.props.testId}*/}
//                     {/*                api={this.api}*/}
//                     {/*                questionsOverHandler={this.questionsOverHandler}*/}
//                     {/*                task={this.state.quiz_task}/>*/}
//                     {/*</CSSTransition>*/}
//                 </div>
//             </div>
//         );
//     }
// }