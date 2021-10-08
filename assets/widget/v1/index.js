// Creates Iframe
function config(block) {
    const testId = block.getAttribute('data-test');
    if (!testId) {
        throw new Error('Error no test specified (e.g data-test="102").');
    }
    const token = block.getAttribute('data-token');
    if (!token) {
        throw new Error('Error no token specified (e.g data-token="PUBLIC_TOKEN").');
    }
    return {
        host: block.getAttribute('data-host') ?? 'https://testonomica.com',
        testId,
        token
    }
}

const block = document.getElementById('testonomica_app');
if (!block) {
    throw new Error('Error tag id "testonomica_app" not found.');
}
const conf = config(block);

const iframe = document.createElement('iframe');
iframe.src = conf.host + '/tests/widget/' + conf.testId + '/?token=' + conf.token;
iframe.loading = 'lazy';
iframe.scrolling = 'no';
iframe.style.border = 'none';
iframe.style.height = 'auto';
iframe.style.width = '100%';
block.appendChild(iframe);

window.onmessage = function (e) {
    if (e.origin !== conf.host) {
        return;
    }
    if (!e.data.hasOwnProperty("frameHeight")) {
        return;
    }
    let height = parseInt(e.data.frameHeight);
    iframe.style.height = height + 'px';
}


//
//
// /**
//  * Minified by jsDelivr using Terser v3.14.1.
//  * Original file: /npm/@testometrika/widget@1.2.1/index.js
//  *
//  * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
//  */
// var testometrika_widget = function() {
//     "use strict";
//     let t = [],
//         some_parameter = true,
//         sid = function() {
//             const t = "testometrika_session";
//
//             function e() {
//                 return localStorage.getItem(t)
//             }
//             return null === e() && localStorage.setItem(t, function(t) {
//                 let e = "wgt",
//                     i = "abcdefghijklmnopqrstuvwxyz0123456789";
//                 for(let o = 0; o < t; o++) e += i.charAt(Math.floor(Math.random() * i.length));
//                 return e
//             }(20)), "?sid=" + e()
//         };
//     return {
//         Test: function(config) {
//             if(!config.key) {
//                 return void console.log("Error not declared key");
//             }
//             config.subdomain ? config.subdomain = `${config.subdomain}.` : config.subdomain = "",
//             config.height_initial || (config.height_initial = "700px"),
//             "boolean" != typeof config.auto_height && (config.auto_height = !0),
//             config.loading || (config.loading = "lazy");
//
//             let n = `${config.key}_iframe`;
//             if(document.getElementById(n)) {
//                 return;
//             }
//             let iframe = document.createElement("iframe");
//             iframe.src = `https://${config.subdomain}testometrika.com/w/${config.key}${sid()}`,
//                 iframe.id = n,
//                 iframe.name = `${config.key}_name`,
//                 !0 === config.auto_height && (iframe.scrolling = "no"),
//                 iframe.setAttribute("loading", config.loading),
//                 iframe.style.border = "none",
//                 iframe.style.width = "100%",
//                 iframe.style.display = "block",
//                 iframe.style.height = config.height_initial,
//                 document.getElementById(config.key).appendChild(iframe),
//                 t[config.key] = new function() {
//                 this.settings = config, this.iframe = iframe
//             }, some_parameter && (some_parameter = !1, window.onmessage = (e => {
//                 if(!e.data.hasOwnProperty("frameHeight")) return;
//                 if(!0 !== t[e.data.key].settings.auto_height) return;
//                 let i = parseInt(e.data.frameHeight),
//                     o = document.getElementById(`${e.data.key}_iframe`); - 1 !== e.origin.indexOf("testometrika.com") && (isNaN(i) || (o.style.height = i + "px"))
//             }))
//         },
//         AutoInit: function() {
//             let tag = document.getElementsByClassName("testometrika_widget");
//             Array.prototype.forEach.call(tag, function(t) {
//                 let config = {};
//                 for(let i, o = 0, n = t.attributes, a = n.length; o < a; o++) {
//                     "id" === (i = n[o]).nodeName && (config.key = i.nodeValue);
//                     let t = i.nodeName.toLowerCase();
//                     0 === t.indexOf("data") && (config[t.replace("data-", "")] = i.nodeValue)
//                 }
//                 void 0 !== config.auto_height && (config.auto_height = "true" === config.auto_height.toLowerCase()), testometrika_widget.Test(config)
//             })
//         },
//         Session: sid
//     }
// }();
//
// testometrika_widget.AutoInit(), document.addEventListener("DOMContentLoaded", function(t) {
//     testometrika_widget.AutoInit()
// }), "undefined" != typeof module && (module.exports = testometrika_widget);
