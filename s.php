<?php
    namespace Facebook\WebDriver;

    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\Remote\RemoteWebDriver;

    require_once('vendor/autoload.php');

    $host = 'http://localhost:4444/wd/hub'; // this is the default
    $capabilities = DesiredCapabilities::phantomjs();

    $driver = RemoteWebDriver::create($host, $capabilities, 5000);
    # set the timeout for implicit waits as 10 seconds
    $driver->manage()->timeouts()->implicitlyWait = 10;
//    $size = new WebDriverDimension(300, 300);
//    $driver->manage()->window()->setSize($size);

    $driver->get('http://24h.pchome.com.tw/prod/DMBE05-A90081FBM');
    //$driver->get('http://24h.pchome.com.tw/prod/DMAG00-A90059M3Q?q=/S/DMAG0K');
    //$driver->get('http://24h.pchome.com.tw/prod/DMAG1B-A83023882?q=/S/DMAG1B');

    $driver->wait(10, 250)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#AlsoBody')));
    $driver->wait(10, 250)->until(
        function () use ($driver) {
            $elmIntro = $driver->findElements(WebDriverBy::cssSelector('#IntroContainer > dd img'));
            $elmIntroLength = count($elmIntro);
            $l = $elmIntroLength;
            for($i = 0; $i < $elmIntroLength; $i++){
                if($elmIntro[$i]->isDisplayed()){
                    $l--;
                }
            }
            $elmAlsoBody = $driver->findElements(WebDriverBy::cssSelector('#AlsoBody > dd > a > img'));
            return count($elmAlsoBody)  >= 4 && $l === 0;
        }, 'wait....timeout'
    );
    $driver->executeScript("document.body.bgColor = 'white';");
    $driver->manage()->window()->maximize();
    $full_screenshot = TakeScreenshot($driver, $capabilities);

    $driver->quit();

    function TakeScreenshot($driver, $capabilities) {
        $screenshot = $capabilities->getBrowserName() . "_" . time() . ".png";
        $driver->takeScreenshot($screenshot);
        return $screenshot;
    }

?>
