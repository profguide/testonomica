(self.webpackChunk=self.webpackChunk||[]).push([[143],{1462:(t,e,n)=>{"use strict";n(2772),n(561),n(9070),n(4916),n(5306),n(9826),n(1539),n(9554),n(4747),n(1058);var r=n(9755),s=n.n(r);function o(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}var i=function(){function t(e,n,r,s,o){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.answers=[],this.count=e,this.progress=n,this.showAnswer=r,this.textOnRight=s,this.textOnWrong=o}var e,n,r;return e=t,(n=[{key:"addAnswer",value:function(t){this.answers.push(t)}},{key:"setAnswer",value:function(t){this.clearAnswers(),this.answers.push(t)}},{key:"getAllAnswers",value:function(){return this.answers}},{key:"getAnswer",value:function(t){return this.answers[t]}},{key:"getAnswerKey",value:function(t){return this.answers.indexOf(t)}},{key:"removeAnswer",value:function(t){this.answers.splice(t,1)}},{key:"clearAnswers",value:function(){this.answers=[]}}])&&o(e.prototype,n),r&&o(e,r),Object.defineProperty(e,"prototype",{writable:!1}),t}();window.TestContext=i,window.question=null,s()((function(){var t=s()("body"),e=(s()("#test-preview-screen"),s()("#test__start-form")),n=s()("#test__ajax-screen"),r=".test__question-form",o=s()(".start-test"),i=s()(".restore-test"),a="#test-question-next-btn",c=".test__option",u=!1;s()("#toast-place").on("click",".toast",(function(t){s()(this).toast("hide")})),o.on("click",(function(t){return console.log("click start btn()"),y(),!1})),i.on("click",(function(t){return m((function(){_(f()+"&restore=1")})),!1})),t.on("click","#test-question-back-btn",(function(t){return _(l()+"&back=1"),!1})),t.on("click",a,(function(t){return _(l()),!1})),t.on("click",".test__btn-again",(function(t){return y(),!1})),t.on("change",'.test__question-form input[name="answer"]',(function(t){return"RATING"===s()(this).data("method")?d(this):"CHECKBOX"===s()(this).data("method")?h(this):"TEXT"===s()(this).data("method")?v():p(t,this),!1}));var l=function(){var e=t.find(r).serialize().replace(/&answer=(.*[^&])?/g,"");return question.getAllAnswers().length>0?question.getAllAnswers().forEach((function(t){e+="&answer[]="+t})):e+="&answer[]= ",e},f=function(){return e.serialize()},d=function(t){question.addAnswer(s()(t).val());var e=s()(t).parents(".test__options_holder").find(c).length,n=s()(t).parent(c);n.fadeOut(200).promise().done((function(){s()(n).remove(),u&&auto()})),1===e&&(s()(c).remove(),_(l()))},h=function(t){var e=question.getAnswerKey(s()(t).val());g(),e>-1?(s()(t).prop("checked",!1),question.removeAnswer(e)):(s()(t).attr("checked","checked"),question.addAnswer(s()(t).val()));var n=s()(r).find('input[type="checkbox"]');if(question.getAllAnswers().length>=parseInt(s()(t).data("limit"))){var o=n.not(":checked");o.attr("disabled","disabled"),o.parent().addClass("disabled")}else n.removeAttr("disabled"),n.parent().removeClass("disabled")},p=function(t,e){question.setAnswer(s()(e).val()),question.showAnswer?(g(),s()(".test__option").each((function(t,n){var r=s()(n).children("input");r[0]===e?(s()(n).addClass("test__option_chosen"),1===s()(e).data("is-correct")?(s()(n).addClass("test__option_correct"),null!=question.textOnRight&&s()(n).find(".test__option-reveal").html(question.textOnRight).show()):(s()(n).addClass("test__option_wrong"),null!=question.textOnWrong&&s()(n).find(".test__option-reveal").html(question.textOnWrong).show())):!0===r.data("is-correct")&&s()(n).addClass("test__option_correct"),r.prop("disabled",!0)}))):_(l())},v=function(){question.clearAnswers(),s()('.test__question-form input[name="answer"]').each((function(t,e){question.addAnswer(s()(e).val())}))},w=new function(){this.next=function(t,e,n){s().ajax("/tests/api/",{data:t,method:"POST","Content-Type":"text/html"}).done((function(t,n,r){e(t,r)})).fail((function(t){console.log("error",t),n()}))}};function g(){s()(a).addClass("test__btn-direct_forced")}function m(t){s()("#test-screens-wrapper > *").hide().promise().done(t)}function y(){m((function(){_(f()+"&clear=1")}))}function _(t){w.next(t,(function(t,e){var r;r=t,n.is(":hidden")?n.html(r).fadeIn():n.hide().promise().done((function(){n.html(r),u?(n.show(),auto()):n.fadeIn()})),e.getResponseHeader("result-uuid")&&window.location.replace("/tests/result/"+e.getResponseHeader("result-uuid")+"/")}),(function(){var t,e,n;t="Произошла ошибка",n="primary",void 0!==(e="danger")&&(n=e),s()("#toast-place").append('<div class="toast fade show" data-delay="2000"><div class="toast-body bg-'+n+' text-white">'+t+' <span aria-hidden="true">&times;</span></div></div>'),s()(".toast").toast({delay:5e3,animation:!0})}))}window.auto=function(){u=!0;var t,e=s()("body").find('.test__question-form input[name="answer"]');if("text"===s()(e[0]).prop("type"))e.each((function(t,e){s()(e).val(42)})),s()("#test-question-next-btn").click();else if("radio"===s()(e[0]).prop("type")){var n=(t=e.length,Math.floor(Math.random()*Math.floor(t)));s()(e[n]).click()}}}))},8533:(t,e,n)=>{"use strict";var r=n(2092).forEach,s=n(9341)("forEach");t.exports=s?[].forEach:function(t){return r(this,t,arguments.length>1?arguments[1]:void 0)}},1194:(t,e,n)=>{var r=n(7293),s=n(5112),o=n(7392),i=s("species");t.exports=function(t){return o>=51||!r((function(){var e=[];return(e.constructor={})[i]=function(){return{foo:1}},1!==e[t](Boolean).foo}))}},9341:(t,e,n)=>{"use strict";var r=n(7293);t.exports=function(t,e){var n=[][t];return!!n&&r((function(){n.call(null,e||function(){return 1},1)}))}},6135:(t,e,n)=>{"use strict";var r=n(4948),s=n(3070),o=n(9114);t.exports=function(t,e,n){var i=r(e);i in t?s.f(t,i,o(0,n)):t[i]=n}},8324:t=>{t.exports={CSSRuleList:0,CSSStyleDeclaration:0,CSSValueList:0,ClientRectList:0,DOMRectList:0,DOMStringList:0,DOMTokenList:1,DataTransferItemList:0,FileList:0,HTMLAllCollection:0,HTMLCollection:0,HTMLFormElement:0,HTMLSelectElement:0,MediaList:0,MimeTypeArray:0,NamedNodeMap:0,NodeList:1,PaintRequestList:0,Plugin:0,PluginArray:0,SVGLengthList:0,SVGNumberList:0,SVGPathSegList:0,SVGPointList:0,SVGStringList:0,SVGTransformList:0,SourceBufferList:0,StyleSheetList:0,TextTrackCueList:0,TextTrackList:0,TouchList:0}},8509:(t,e,n)=>{var r=n(317)("span").classList,s=r&&r.constructor&&r.constructor.prototype;t.exports=s===Object.prototype?void 0:s},9554:(t,e,n)=>{"use strict";var r=n(2109),s=n(8533);r({target:"Array",proto:!0,forced:[].forEach!=s},{forEach:s})},2772:(t,e,n)=>{"use strict";var r=n(2109),s=n(1702),o=n(1318).indexOf,i=n(9341),a=s([].indexOf),c=!!a&&1/a([1],1,-0)<0,u=i("indexOf");r({target:"Array",proto:!0,forced:c||!u},{indexOf:function(t){var e=arguments.length>1?arguments[1]:void 0;return c?a(this,t,e)||0:o(this,t,e)}})},561:(t,e,n)=>{"use strict";var r=n(2109),s=n(7854),o=n(1400),i=n(9303),a=n(6244),c=n(7908),u=n(5417),l=n(6135),f=n(1194)("splice"),d=s.TypeError,h=Math.max,p=Math.min,v=9007199254740991,w="Maximum allowed length exceeded";r({target:"Array",proto:!0,forced:!f},{splice:function(t,e){var n,r,s,f,g,m,y=c(this),_=a(y),x=o(t,_),b=arguments.length;if(0===b?n=r=0:1===b?(n=0,r=_-x):(n=b-2,r=p(h(i(e),0),_-x)),_+n-r>v)throw d(w);for(s=u(y,r),f=0;f<r;f++)(g=x+f)in y&&l(s,f,y[g]);if(s.length=r,n<r){for(f=x;f<_-r;f++)m=f+n,(g=f+r)in y?y[m]=y[g]:delete y[m];for(f=_;f>_-r+n;f--)delete y[f-1]}else if(n>r)for(f=_-r;f>x;f--)m=f+n-1,(g=f+r-1)in y?y[m]=y[g]:delete y[m];for(f=0;f<n;f++)y[f+x]=arguments[f+2];return y.length=_-r+n,s}})},9070:(t,e,n)=>{var r=n(2109),s=n(9781),o=n(3070).f;r({target:"Object",stat:!0,forced:Object.defineProperty!==o,sham:!s},{defineProperty:o})},4747:(t,e,n)=>{var r=n(7854),s=n(8324),o=n(8509),i=n(8533),a=n(8880),c=function(t){if(t&&t.forEach!==i)try{a(t,"forEach",i)}catch(e){t.forEach=i}};for(var u in s)s[u]&&c(r[u]&&r[u].prototype);c(o)}},t=>{t.O(0,[865,498],(()=>{return e=1462,t(t.s=e);var e}));t.O()}]);