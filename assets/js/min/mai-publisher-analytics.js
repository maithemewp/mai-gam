(()=>{function r(r){return function(r){if(Array.isArray(r))return t(r)}(r)||function(r){if("undefined"!=typeof Symbol&&null!=r[Symbol.iterator]||null!=r["@@iterator"])return Array.from(r)}(r)||function(r,n){if(r){if("string"==typeof r)return t(r,n);var e={}.toString.call(r).slice(8,-1);return"Object"===e&&r.constructor&&(e=r.constructor.name),"Map"===e||"Set"===e?Array.from(r):"Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e)?t(r,n):void 0}}(r)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function t(r,t){(null==t||t>r.length)&&(t=r.length);for(var n=0,e=Array(t);n<t;n++)e[n]=r[n];return e}var n,e,o,a;n=window._paq=window._paq||[],e=maiPubAnalyticsVars.analytics,o=e[0],a=e.slice(1),function(){for(var r in n.push(["setTrackerUrl",o.url+"matomo.php"]),n.push(["setSiteId",o.id]),a)n.push(["addTracker",a[r].url+"matomo.php",a[r].id]);var t=document,e=t.createElement("script"),i=t.getElementsByTagName("script")[0];e.async=!0,e.src=o.url+"matomo.js",i.parentNode.insertBefore(e,i)}(),window.matomoAsyncInit=function(){for(var t in e)try{var n=Matomo.getTracker(e[t].url+"matomo.php",e[t].id);for(var o in e[t].toPush){var a=e[t].toPush[o][0],i=e[t].toPush[o].slice(1);(i=i||null)?n[a].apply(n,r(i)):n[a]()}e[t].ajaxUrl&&e[t].body&&fetch(e[t].ajaxUrl,{method:"POST",credentials:"same-origin",headers:{"Content-Type":"application/x-www-form-urlencoded","Cache-Control":"no-cache"},body:new URLSearchParams(e[t].body)}).then((function(r){if(!r.ok)throw new Error(r.statusText);return r.json()})).then((function(r){})).catch((function(r){console.log(r.name+", ",r.message)}))}catch(r){console.log(r)}}})();