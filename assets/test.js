import Testonomica from 'testonomica_api/src/index';
import Config from 'testonomica_api/src/config';
import {EVENT_FINISH, EVENT_LOADED} from 'testonomica_api/src/events';
import 'testonomica_api/src/style.scss';

const INIT_AUTO = 'auto';
const INIT_MANUAL = 'manual';

const tag = document.getElementById('testonomica_app');
const testId = tag.getAttribute('data-test');
if (!testId) {
    throw new Error('tag must include attribute: data-test');
}
const host = tag.getAttribute('data-host') ?? 'https://testonomica.com';
const token = tag.getAttribute('data-token') ?? null;
const init = tag.getAttribute('data-init') ?? null;

// настройки теста
const config = new Config({
    showResultAfterLoad: tag.getAttribute('data-show-result-after-load') ?? true
});

window.testonomica = new Testonomica(testId, host, token);

// получен результат в ходе прохождения теста
window.testonomica.addEventListener(EVENT_FINISH, function (e) {
    parent.postMessage({name: EVENT_FINISH, key: e.key}, '*');
});
window.testonomica.addEventListener(EVENT_LOADED, function (e) {
    parent.postMessage({name: EVENT_LOADED}, '*');
});

if (!init || init === INIT_AUTO) {
    window.testonomica.createApp(tag, config);
}