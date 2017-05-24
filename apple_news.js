"use strict";

function getFileName() {
    var d = new Date();
    var date = [
        d.getUTCFullYear(),
        d.getUTCMonth() + 1,
        d.getUTCDate()
    ];
    var time = [
        d.getHours() <= 9 ? '0' + d.getHours() : d.getHours(),
        d.getMinutes() <= 9 ? '0' + d.getMinutes() : d.getMinutes(),
        d.getSeconds() <= 9 ? '0' + d.getSeconds() : d.getSeconds(),
        d.getMilliseconds()
    ];

    return date.join('-') + '_' + time.join('-') + "";
}

function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 20000, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    console.log("'waitFor()' timeout");
                    phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 250); //< repeat check every 250ms
};

var system = require('system');
var args = system.args;

if (args.length === 0) {
    console.log('Try to pass some arguments when invoking this script!');
    phantom.exit();
} else {
    var hasUrl = false;
    var url = '';
    args.forEach(function(arg, i) {
        console.log(i + ': ' + arg);
        if(arg.match(/^http:\/\//gi))
        {
            url = arg.replace(/^http:\/\/www/gi, "http://m");;
            hasUrl = true;
        }
    });
    console.log('Get Url: ' + url);
    if(!hasUrl)phantom.exit();
}

var page = new WebPage();
page.viewportSize = {
    width: 410,
    height: 736 //再大張就很麻煩了
};

page.onLoadFinished = function() {
    console.log("page.onLoadFinished");
    page.evaluate(function() {
        //document.body.bgColor = 'white';
        //$('body').css("font-family", "WenQuanYi Zen Hei Mono");
    });
};

page.open(url, function (status) {
    // Check for page load success
    if (status !== "success") {
        console.log("Unable to access network");
    } else {
        console.log("Start page");
        waitFor(function() {
            // Check in the page if a specific element is now visible
            return page.evaluate(function() {
                return $("body > div.nm-base.nm-page-time.ui-page.ui-body-c.ui-page-panel.ui-page-active > div.ui-panel-content-wrap.ui-body-c.ui-panel-animate.ui-panel-content-wrap-closed > article > div.nm-article > div.nm-article-body > div:nth-child(1)").is(":visible");
            });
        }, function() {
            console.log("The #Intro should be visible now.");
            window.setTimeout(function () {
                console.log('start capture image...');
                console.log(page.evaluate(function(){
                    $("#Cover").css("display", "none");
                    $("#recommend_appledaily").css("display", "none");
                    $(".nm-ad").css("display", "none");
                    $(".nm-fb-comments").css("display", "none");
                    $(".nm-prevnext-articles").css("display", "none");
                }));
                console.log(page.evaluate(function(){
                    return JSON.stringify({
                        "document.body.scrollHeight": document.body.scrollHeight,
                        "document.body.offsetHeight": document.body.offsetHeight,
                        "document.documentElement.clientHeight": document.documentElement.clientHeight,
                        "document.documentElement.scrollHeight": document.documentElement.scrollHeight
                    }, undefined, 4);
                }));
                page.clipRect = { top: 0, left: 0, width: page.evaluate(function(){return document.documentElement.scrollWidth;}), height: page.evaluate(function(){return document.documentElement.scrollHeight;}) };
                page.render('appledaily_' + getFileName() + '.jpg', {format: 'jpeg', quality: '85'});
                phantom.exit();
            }, 100);
        });
    }
});

