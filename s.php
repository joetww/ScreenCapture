<?php
namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once('vendor/autoload.php');

$host = 'http://localhost:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::phantomjs();

$driver = RemoteWebDriver::create($host, $capabilities, 5000);
# set the timeout for implicit waits as 10 seconds
$driver->manage()->timeouts()->implicitlyWait = 0;
//    $size = new WebDriverDimension(300, 300);
//    $driver->manage()->window()->setSize($size);

$driver->get('http://24h.pchome.com.tw/prod/DMBE05-A90081FBM');
//$driver->get('http://24h.pchome.com.tw/prod/DMAG00-A90059M3Q?q=/S/DMAG0K');
//$driver->get('http://24h.pchome.com.tw/prod/DMAG1B-A83023882?q=/S/DMAG1B');

$driver->wait(10, 100)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#AlsoBody')));
$driver->wait(10, 10)->until(
    function () use ($driver) {
        $elmIntro = $driver->findElements(WebDriverBy::cssSelector('#IntroContainer > dd img'));
        $elmIntroLength = count($elmIntro);
        $lI = $elmIntroLength;
        for($i = 0; $i < $elmIntroLength; $i++){
            if($elmIntro[$i]->isDisplayed()){
                $lI--;
            }
        }
        $elmAlsoBody = $driver->findElements(WebDriverBy::cssSelector('#AlsoBody > dd > a > img'));
        $elmAlsoBodyLength = count($elmAlsoBody);
        $lA = $elmAlsoBodyLength;
        for($i = 0; $i < $elmAlsoBodyLength; $i++){
            if($elmAlsoBody[$i]->isDisplayed()){
                $lA--;
            }else
            {
                echo $elmAlsoBody[$i]->getAttribute('src') ."\n";
            }
        }
        echo "A: ".$lA."\tI: ".$lI."\n";
        return $lI === 0 && $lA <= 1;
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
