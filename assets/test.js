import Testonomica from 'testonomica_api/src/Testonomica';
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

window.testonomica = new Testonomica(testId, host, token);
if (!init || init === INIT_AUTO) {
    window.testonomica.createApp(tag);
}