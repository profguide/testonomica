(self.webpackChunk=self.webpackChunk||[]).push([[328],{3445:(e,t,n)=>{"use strict";n(9826),n(1539);var i=n(9755),l=n.n(i);l()((function(){var e={SUM:"Набранная сумма всех числовых значений",SCALE:"Процент всех числовых значений от максимума",NON_NEGATIVE_ANSWER_VALUES_SUM:"Сумма всех неотрицательных значений (числа, строки)"};l()("input[name*='[main][value]']").each((function(){var t=l()(this).val(),n="«"+t+"»";e["REPEATS."+t+".sum"]="Ответ "+n+": кол-во ответов",e["REPEATS."+t+".percentage"]="Ответ "+n+": процент кол-ва от общего числа ответов",e["REPEATS."+t+".percentage_value"]="Ответ "+n+": процент кол-ва от максимума ответа "+n}));var t=document.createElement("select");l().each(e,(function(e,n){l()(t).append(l()("<option>").attr("value",e).text(n))}));l()("input[name*='[variableName]']").each((function(){var e,n,i;e=this,n=l()(e).parent(),i=t.cloneNode(),l()(i).addClass(e.className),l()(i).attr("name",l()(e).attr("name")),l()(i).attr("id",l()(e).attr("id")),l()(i).html(l()(t).html()),l()(i).children("option[value='"+l()(e).val()+"']").prop("selected",!0),l()(e).remove(),l()(n).append(i)}))})),l()((function(){var e=".question-widget",t=".question-checkbox-show-answer",n=".question-show-answer-block",i=function(t){l()(t).is(":checked")?l()(t).closest(e).find(n).show():l()(t).closest(e).find(n).hide()};l()("body").on("click",t,(function(){i(this)})),l()(t).each((function(){i(this)}))})),l()((function(){l()("body").on("click",".optional-field > label, .optional-field > legend",(function(){l()(this).parent().find(".form-widget:first").toggle()})),l()(".optional-field > .form-widget").each((function(){var e=!0;l()(this).find(".form-control").each((function(t,n){l()(n).val().length>0&&(e=!1)})),e&&l()(this).hide()}))}));n(1058),n(4916),n(4723),n(5306),n(7207);l()((function(){l()(".field-collection-copy-button").on("click",(function(){var e=l()("#Test_questions"),t=e.children().last().html();l()(this).siblings(".field-collection-add-button").trigger("click");var n,i=e.children().last(),o=(n=i.html(),parseInt(n.match(/Test_questions_([0-9]+)/)[1]));console.log(o);var a=t.replaceAll(/Test_questions_[0-9]+/g,"Test_questions_"+o).replaceAll(/Test\[questions\]\[[0-9]+\]/g,"Test[questions]["+o+"]");i.html(a)}))}))},7850:(e,t,n)=>{var i=n(111),l=n(4326),o=n(5112)("match");e.exports=function(e){var t;return i(e)&&(void 0!==(t=e[o])?!!t:"RegExp"==l(e))}},4723:(e,t,n)=>{"use strict";var i=n(6916),l=n(7007),o=n(9670),a=n(7466),r=n(1340),c=n(4488),s=n(8173),u=n(1530),f=n(7651);l("match",(function(e,t,n){return[function(t){var n=c(this),l=null==t?void 0:s(t,e);return l?i(l,t,n):new RegExp(t)[e](r(n))},function(e){var i=o(this),l=r(e),c=n(t,i,l);if(c.done)return c.value;if(!i.global)return f(i,l);var s=i.unicode;i.lastIndex=0;for(var h,d=[],p=0;null!==(h=f(i,l));){var v=r(h[0]);d[p]=v,""===v&&(i.lastIndex=u(l,a(i.lastIndex),s)),p++}return 0===p?null:d}]}))},8757:(e,t,n)=>{"use strict";var i=n(2109),l=n(7854),o=n(6916),a=n(1702),r=n(4488),c=n(614),s=n(7850),u=n(1340),f=n(8173),h=n(7066),d=n(647),p=n(5112),v=n(1913),g=p("replace"),m=RegExp.prototype,E=l.TypeError,b=a(h),w=a("".indexOf),x=a("".replace),_=a("".slice),k=Math.max,A=function(e,t,n){return n>e.length?-1:""===t?n:w(e,t,n)};i({target:"String",proto:!0},{replaceAll:function(e,t){var n,i,l,a,h,p,T,q,S,N=r(this),R=0,y=0,I="";if(null!=e){if((n=s(e))&&(i=u(r("flags"in m?e.flags:b(e))),!~w(i,"g")))throw E("`.replaceAll` does not allow non-global regexes");if(l=f(e,g))return o(l,e,N,t);if(v&&n)return x(u(N),e,t)}for(a=u(N),h=u(e),(p=c(t))||(t=u(t)),T=h.length,q=k(1,T),R=A(a,h,0);-1!==R;)S=p?u(t(h,R,a)):d(h,a,R,[],void 0,t),I+=_(a,y,R)+S,y=R+T,R=A(a,h,R+q);return y<a.length&&(I+=_(a,y)),I}})},7207:(e,t,n)=>{n(8757)}},e=>{e.O(0,[109,487],(()=>{return t=3445,e(e.s=t);var t}));e.O()}]);