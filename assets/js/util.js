export function language() {
    return document.documentElement.lang;
}

export function localeUrl(path) {
    const lang = language();
    if (lang === 'en') {
        if (path.charAt(0) === '/') {
            path = path.substring(1);
        }
        return '/en/' + path;
    }
    return path;
}

export function t(ru, en) {
    const lang = language();
    if (lang === 'en') {
        return en;
    }
    return ru;
}