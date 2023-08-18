import {isCookieEnabled, parseConfigFromTag, Testonomica} from 'testonomica_api/src/index';
import {INIT_AUTO} from 'testonomica_api/src/const'
import ProgressStorage from "testonomica_api/src/service/storage/ProgressStorage";
import ProgressFirebaseStorage from "testonomica_api/src/service/storage/ProgressFirebaseStorage";
import 'testonomica_api/src/style.scss';
import {NO_MORE_QUESTIONS_EVENT} from "../../testonomica_api/src/events";
import Api from "./js/api";
import {localeUrl} from "./js/util";

const tag = document.getElementById('testonomica_app');
const config = parseConfigFromTag(tag);

let storage = null;
if (isCookieEnabled()) {
    storage = new ProgressStorage(config.getTestId());
} else {
    /** sid will be generated and saved in the localStorage and used if there is no sid in the tag */
    const sid = tag.getAttribute('data-sid');
    if (!sid) {
        throw Error('Error: sid has to be specified.');
    }
    storage = new ProgressFirebaseStorage(config.getTestId(), sid);
}

window.testonomica = new Testonomica(storage, config.getTestId(), config.getHost(), config.getToken());
window.testonomica.addEventListener(NO_MORE_QUESTIONS_EVENT, function () {
    const api = new Api(config.getTestId());
    storage.getAnswers()
        .then(progress => api.saveProgress(progress))
        .then(key => {
            storage.clear();
            window.location.href = localeUrl(`/tests/result/${key}/`);
        });
});
if (config.getInit() === INIT_AUTO) {
    window.testonomica.createApp(tag, config);
}