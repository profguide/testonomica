import {HOST} from "../const";
import axios from "axios";
import ProgressStorage from "./storage/ProgressStorage";
import QuestionResponseHydrator from "./types/QuestionResponseHydrator";
import Answer from "./types/Answer";

/**
 * Low-level API: requests, storing data
 */
export default class ServiceApi {
    constructor(config = {}) {
        if (!config['testId']) {
            throw new Error('testId must be defined.');
        }
        this.testId = config['testId'];
        this.host = config['host'] ?? HOST;
        this.token = config['token'];
        this.storage = new ProgressStorage(config.testId);
        this.test = null; // loaded brief about test
        this.question = null; // loaded question (current question)
    }

    description() {
        // information is always free
        return axios({
            method: 'get',
            url: this.buildUrl('/info/' + this.testId + '/'),
            responseType: 'json',
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            this.test = {
                name: response.data.name,
                description: response.data.description,
                duration: response.data.duration,
                length: response.data.length,
            }
            return this.test;
        })
    }

    status() {
        return this.storage.getStatus();
    }

    progressFull() {
        return this.test.length === this.storage.getLength();
    }

    saveResult() {
        console.log('Saving...');
        return axios.post(this.buildUrl('/save/' + this.testId + '/'), {
            progress: this.storage.getAnswers(),
        }, {
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            const key = response.data.key;
            this.storage.setFinished(key);
            return key;
        });
    }

    result() {
        return axios.get(this.buildUrl('/result/' + this.testId + '/?key=' + this.storage.resultKey()), {
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            return response;
        });
    }

    resultKey() {
        return this.storage.resultKey();
    }

    clear() {
        this.storage.clear();
    }

    /**
     * First query should be made throughout this.
     * Если autoRestore=true, отправляет restore
     * Иначе отправляет restart.
     * С другой стороны next может делать то же самое.
     */
    first() {
        return axios({
            method: 'get',
            url: this.buildUrl('/first/' + this.testId + '/'),
            responseType: 'json',
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            this.question = (new QuestionResponseHydrator()).hydrate(response);
            return this.question;
        })
    }

    next() {
        let id;
        if (this.question) {
            id = this.question.id;
        } else {
            const answer = this.storage.getLastAnswer();
            id = answer.questionId;
        }
        return axios({
            method: 'get',
            url: this.buildUrl('/next/' + this.testId + '/?q=' + id),
            responseType: 'json',
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            this.question = (new QuestionResponseHydrator()).hydrate(response);
            return this.question;
        });
    }

    prev() {
        return axios({
            method: 'get',
            url: this.buildUrl('/prev/' + this.testId + '/?q=' + this.question.id),
            responseType: 'json',
            headers: {'token': this.token}
        }).then(response => {
            this.token = response.headers['x-token'];
            this.question = (new QuestionResponseHydrator()).hydrate(response);
            return this.question;
        });
    }

    addAnswer(value) {
        this.storage.addAnswer(Answer.createImmutable(this.question.id, value))
    }

    buildUrl(path) {
        return this.host + '/tests/api/v1' + path;
    }
}