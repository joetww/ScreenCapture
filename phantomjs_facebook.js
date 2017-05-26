console.log("got here");
var page2 = require('webpage').create();

page2.onConsoleMessage = function(msg) {
    console.log(msg);
};


page2.open("http://www.facebook.com", function(status) {
    if ( status === "success" ) {
        page2.evaluate(function() {
            document.querySelector("input[name='email']").value = "joe.yue@gmail.com";
            document.querySelector("input[name='pass']").value = "649598bcf7";
            document.getElementsByName('login')[0].click();

            console.log("Login submitted!");
        });
        window.setTimeout(function () {
            page2.render('colorwheel.png');
            phantom.exit();
        }, 2500);
    }
});
