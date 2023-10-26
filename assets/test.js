import {localeUrl} from "./js/util";

window.initWidget = function (test, host) {
    window.tncw.init({
        provider: 'testonomica',
        containerId: 'testonomica_app',
        test: test,
        host: host,
        handlers: {
            loaded: function () {
                document.getElementById('app-preload-screen').remove();
            },
            finish: function (e) {
                const key = e.result_key;
                window.location.href = localeUrl(`/tests/result/${key}/`);
            }
        }
    });
}