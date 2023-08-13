import $ from 'jquery'

export default function Api(testId) {
    return {
        // saves results and returns the key
        saveProgress: function (progress) {
            return new Promise(resolve => {
                $.post({
                    url: `/tests/api/v1/save/${testId}/`,
                    data: {testId, progress} // true | 0 = 1, false | 0 = 0
                }).then(response => {
                    resolve(response.key);
                }).fail(() => {
                    alert('Не удалось сохранить прогресс.');
                });
            })
        },

        getResultByKey: function (key) {
            return new Promise(resolve => {
                $.get({
                    url: `/tests/api/v1/result/${testId}/?key=${key}`
                }).then(response => {
                    resolve(response);
                }).fail(() => {
                    resolve(null);
                });
            });
        }
    }
}