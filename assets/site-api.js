import ReactDOM from "react-dom";
import React from 'react';
import App from "./api/v1/site/app/App";
import {HOST} from "./api/v1/const";

import './api/v1/style.scss'

const tag = document.getElementById('testonomica_app');
const testId = tag.getAttribute('data-test');
if (!testId) {
    throw new Error('tag must include attribute: data-test');
}
const host = tag.getAttribute('data-host') ?? HOST;
const token = tag.getAttribute('data-token') ?? null;

window.testonomicaApp = ReactDOM.render(<App testId={testId} host={host} token={token}/>, tag);
// window.testonomicaApp.addEventListener('finish', function (key) {
//     window.location.href = '/tests/result/' + key + '/';
// })