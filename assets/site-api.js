import ReactDOM from "react-dom";
import React from 'react';
import App from "./api/v1/site/App";
import {HOST} from "./api/v1/const";
import './api/v1/style.scss'

const INIT_AUTO = 'auto';
const INIT_MANUAL = 'manual';

const tag = document.getElementById('testonomica_app');
const testId = tag.getAttribute('data-test');
if (!testId) {
    throw new Error('tag must include attribute: data-test');
}
const host = tag.getAttribute('data-host') ?? HOST;
const token = tag.getAttribute('data-token') ?? null;
const init = tag.getAttribute('data-init') ?? null;

class TncEventDispatcher {
    constructor() {
        this.listeners = {};
    }

    addEventListener(name, callback) {
        this.listeners[name] = callback
    }

    dispatchEvent(e) {
        console.log(this.listeners);
        console.log(this.listeners[e.type]);
        if (this.listeners[e.type]) {
            this.listeners[e.type](e.detail);
        }
    }
}

class Testonomica {
    constructor() {
        this.dispatcher = new TncEventDispatcher();
    }

    createApp() {
        ReactDOM.render(<App testId={testId} host={host} token={token} dispatcher={this.dispatcher}/>, tag);
    }

    addEventListener(name, callback) {
        this.dispatcher.addEventListener(name, callback);
    }
}

const testonomica = new Testonomica();
if (!init || init === INIT_AUTO) {
    testonomica.createApp();
}

window.testonomica = testonomica;
// how to use:
// var api = window.testonomica;
// api.addEventListener('finish', (data) => {alert(data.key)})
// api.createApp();