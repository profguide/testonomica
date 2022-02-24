export function randString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;

    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

/**
 * Checks if cookie are enabled
 * @returns {boolean}
 */
export function checkCookie() {
    let cookieEnabled = navigator.cookieEnabled;
    if (!cookieEnabled) {
        document.cookie = "testcookie";
        cookieEnabled = document.cookie.indexOf("testcookie") !== -1;
    }
    return cookieEnabled
}

/**
 * Creates constant session id, which may be used for storing in the FireBase or on the server.
 * For now in not used because if localStorage disabled, then the other technology will be used.
 * @returns {string}
 */
export function session() {
    const STORAGE_NAME = 'tnc_sid';

    function init() {
        if (!get()) {
            set();
        }
        return get();
    }

    function get() {
        return localStorage.getItem(STORAGE_NAME);
    }

    function set() {
        return localStorage.setItem(STORAGE_NAME, randString(12));
    }

    return init();
}