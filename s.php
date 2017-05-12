<?php
    namespace Facebook\WebDriver;

    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    //use Facebook\WebDriver\Remote\RemoteWebElement;
    //use Facebook\WebDriver\WebDriverWindow;

    require_once('vendor/autoload.php');

    // start Firefox with 5 second timeout
    $host = 'http://localhost:4444/wd/hub'; // this is the default
    //$capabilities = DesiredCapabilities::chrome();
    //$capabilities = DesiredCapabilities::firefox();
    $capabilities = DesiredCapabilities::phantomjs();

    $driver = RemoteWebDriver::create($host, $capabilities, 5000);
    # set the timeout for implicit waits as 10 seconds
    $driver->manage()->timeouts()->implicitlyWait = 10;
    $size = new WebDriverDimension(300, 300);
    $driver->manage()->window()->setSize($size);
    //var_dump($driver->manage()->window()->getSize());

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
//    $companyFoot = $driver->findElement(WebDriverBy::cssSelector('dl.msg_box > dd:nth-child(4)'));
//    $companyHead = $driver->findElement(WebDriverBy::cssSelector('#HEADER'));
//    $companyBody = $driver->findElement(WebDriverBy::cssSelector('body'));
    //$size = new WebDriverDimension($companyBody->getLocation()->getX() + $companyBody->getSize()->getWidth() + 40, $companyBody->getLocation()->getY()+$companyBody->getSize()->getHeight() + 20);
//    $size = new WebDriverDimension(1400, $companyBody->getLocation()->getY() + $companyBody->getSize()->getHeight() + 30);
//    $size = new WebDriverDimension($companyHead->getLocation()->getX() + $companyHead->getSize()->getWidth() + 20, $companyFoot->getLocation()->getY()+$companyFoot->getSize()->getHeight() + 100);
    //$size = new WebDriverDimension($companyHead->getLocation()->getX() + $companyHead->getSize()->getWidth() + 300, 32168 * 2);
    //    $driver->manage()->window()->setSize($size);
    $driver->manage()->window()->maximize();
    //var_dump($driver->manage()->window()->getSize());
    $full_screenshot = TakeScreenshot($driver, $capabilities);

    $driver->quit();

    function TakeScreenshot($driver, $capabilities) {
        $screenshot = $capabilities->getBrowserName() . "_" . time() . ".png";
        $driver->takeScreenshot($screenshot);
        return $screenshot;
    }

?>
