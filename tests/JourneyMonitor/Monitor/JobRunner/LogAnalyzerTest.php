<?php

namespace JourneyMonitor\Monitor\JobRunner;

class LogAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testLineContainsScreenshot()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertTrue($la->lineContainsScreenshot($line));
    }

    public function testLineDoesNotContainScreenshot()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - i like screenshots: foo';
        $la = new LogAnalyzer();
        $this->assertFalse($la->lineContainsScreenshot($line));
    }

    public function testGetScreenshotFilenameFromLine()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'test_20150522_204820088_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromLineWithLongerTimestamp()
    {
        $line = '[2016-04-05 11:20:31.211 +02:00] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromPartlyCorrupted()
    {
        $line = '[2016-04-05 11:20[INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromLineFails()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - i like screenshots: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertFalse($la->getScreenshotFilenameFromLine($line));
    }
    
    public function testPageloadTimeoutOccuredInErrorcode3Log() {
        $output = <<<EOT
{"port":19465}[2016-11-16 13:20:11.765 +01:00] [INFO] Start: Selenese Runner 2.6.0
[2016-11-16 13:20:12.163 +01:00] [INFO] Command line arguments: [--new-instance] [--profile /var/tmp/journeymonitor-firefox-profile-9445]
[2016-11-16 13:20:21.403 +01:00] [INFO] Initial window size: 1920x1200
[2016-11-16 13:20:21.404 +01:00] [INFO] Initialized: FirefoxDriver
[2016-11-16 13:20:21.542 +01:00] [INFO] Screenshot on fail directory: /var/tmp/journeymonitor-screenshots
[2016-11-16 13:20:21.576 +01:00] [INFO] Timeout: 30000 ms
[2016-11-16 13:20:22.310 +01:00] [INFO] Start: TestSuite[galeria-de-suche-nach-hose] (/var/tmp/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF.html)
[2016-11-16 13:20:22.358 +01:00] [INFO] Existing driver found.
[2016-11-16 13:20:22.394 +01:00] [INFO] Current speed: 0 ms/command
[2016-11-16 13:20:22.394 +01:00] [INFO] Start: TestCase[galeria-de-suche-nach-hose] (/var/tmp/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF.html)
[2016-11-16 13:20:22.395 +01:00] [INFO] baseURL: https://www.galeria-kaufhof.de
[2016-11-16 13:20:22.432 +01:00] [INFO] <1> Command#1: open("/")
[2016-11-16 13:20:31.707 +01:00] [INFO] - [Success] URL: [https://www.galeria-kaufhof.de/] / Title: [GALERIA Kaufhof: Shop für Mode, Spielwaren & Haushalt]
[2016-11-16 13:20:31.707 +01:00] [INFO] - Cookie: _ga=[GA1.2.1253017344.1479298826] (domain=.galeria-kaufhof.de, path=/, expire=2017-01-28 13:20:26 +01:00)
[2016-11-16 13:20:31.715 +01:00] [INFO] <2> Command#2: type("id=gk-header__search__input", "Hose")
[2016-11-16 13:20:32.240 +01:00] [INFO] - [Success]
[2016-11-16 13:20:32.240 +01:00] [INFO] <3> Command#3: clickAndWait("id=gk-header__search__button")
[2016-11-16 13:21:42.990 +01:00] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF_20161116_132102335_3_fail.png
[2016-11-16 13:21:42.990 +01:00] [INFO] [[ATTACHMENT|/var/tmp/journeymonitor-screenshots/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF_20161116_132102335_3_fail.png]]
[2016-11-16 13:21:43.070 +01:00] [ERROR] Command#3: clickAndWait("id=gk-header__search__button") => [Failure: Timed out waiting for page load. / Command duration or timeout: 30.04 seconds / Build info: version: 'unknown', revision: 'unknown', time: 'unknown' / System info: host: 'v22015051223725490', ip: '37.120.178.155', os.name: 'Linux', os.arch: 'amd64', os.version: '3.13.0-96-generic', java.version: '1.8.0_111' / Driver info: org.openqa.selenium.firefox.FirefoxDriver / Capabilities [{applicationCacheEnabled=true, rotatable=false, handlesAlerts=true, databaseEnabled=true, version=45.4.0, platform=LINUX, nativeEvents=false, acceptSslCerts=true, webStorageEnabled=true, locationContextEnabled=true, browserName=firefox, takesScreenshot=true, javascriptEnabled=true, cssSelectorsEnabled=true}] / Session ID: e498cff3-e4b2-4f7d-9ea8-f11d6b562195] URL: [https://www.galeria-kaufhof.de/search?q=Hose] / Title: [Suchergebnis für Hose | GALERIA Kaufhof]
[2016-11-16 13:21:43.071 +01:00] [ERROR] - Cookie: _ga=[GA1.2.1253017344.1479298826] (domain=.galeria-kaufhof.de, path=/, expire=2017-01-28 13:20:34 +01:00)
[2016-11-16 13:21:43.079 +01:00] [INFO] End(1min/20,680sec): TestCase[galeria-de-suche-nach-hose] (/var/tmp/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF.html)
[2016-11-16 13:21:43.079 +01:00] [INFO] End(0,000sec): TestSuite[galeria-de-suche-nach-hose] (/var/tmp/journeymonitor-testcase-C45770FE-7F90-4348-AA0E-3C8175519EFF.html)
[2016-11-16 13:21:43.080 +01:00] [INFO] Exit code: 3
[2016-11-16 13:21:43.167 +01:00] [INFO] Quit: FirefoxDriver
EOT;
        $la = new LogAnalyzer();
        $this->assertTrue($la->pageloadTimeoutOccured(explode("\n", $output)));
    }

    public function testPageloadTimeoutOccuredInErrorcode4Log()
    {
        $output = <<<EOT
{"port":42648}[2016-11-15 05:30:05.778 +01:00] [INFO] Start: Selenese Runner 2.6.0
[2016-11-15 05:30:06.146 +01:00] [INFO] Command line arguments: [--new-instance] [--profile /var/tmp/journeymonitor-firefox-profile-24835]
[2016-11-15 05:30:14.336 +01:00] [INFO] Initial window size: 1920x1200
[2016-11-15 05:30:14.341 +01:00] [INFO] Initialized: FirefoxDriver
[2016-11-15 05:30:14.402 +01:00] [INFO] Screenshot on fail directory: /var/tmp/journeymonitor-screenshots
[2016-11-15 05:30:14.413 +01:00] [INFO] Timeout: 30000 ms
[2016-11-15 05:30:14.999 +01:00] [INFO] Start: TestSuite[complex] (/var/tmp/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF.html)
[2016-11-15 05:30:15.022 +01:00] [INFO] Existing driver found.
[2016-11-15 05:30:15.043 +01:00] [INFO] Current speed: 0 ms/command
[2016-11-15 05:30:15.043 +01:00] [INFO] Start: TestCase[complex] (/var/tmp/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF.html)
[2016-11-15 05:30:15.043 +01:00] [INFO] baseURL: https://www.inno.be/nl-be
[2016-11-15 05:30:15.060 +01:00] [INFO] <1> Command#1: open("/")
[2016-11-15 05:30:45.483 +01:00] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF_20161115_053045119_1_fail.png
[2016-11-15 05:30:45.483 +01:00] [INFO] [[ATTACHMENT|/var/tmp/journeymonitor-screenshots/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF_20161115_053045119_1_fail.png]]
[2016-11-15 05:30:45.551 +01:00] [ERROR] Command#1: open("/") => [Error: TimeoutException - Timed out waiting for page load.
Command duration or timeout: 30.03 seconds
Build info: version: 'unknown', revision: 'unknown', time: 'unknown'
System info: host: 'v22015051223725490', ip: '37.120.178.155', os.name: 'Linux', os.arch: 'amd64', os.version: '3.13.0-96-generic', java.version: '1.8.0_91'
Driver info: org.openqa.selenium.firefox.FirefoxDriver
Capabilities [{applicationCacheEnabled=true, rotatable=false, handlesAlerts=true, databaseEnabled=true, version=45.4.0, platform=LINUX, nativeEvents=false, acceptSslCerts=true, webStorageEnabled=true, locationContextEnabled=true, browserName=firefox, takesScreenshot=true, javascriptEnabled=true, cssSelectorsEnabled=true}]
Session ID: 661018dc-8425-4d3e-a0fa-9d22e4999adc (ErrorHandler.createThrowable(ErrorHandler.java:206) / ErrorHandler.throwIfResponseFailed(ErrorHandler.java:158) / RemoteWebDriver.execute(RemoteWebDriver.java:678) / RemoteWebDriver.get(RemoteWebDriver.java:316) / Open.executeImpl(Open.java:36) / AbstractCommand.execute(AbstractCommand.java:145) / CommandList.doCommand(CommandList.java:96) / ScreenshotInterceptor.invoke(ScreenshotInterceptor.java:18) / AbstractDoCommandInterceptor.invoke(AbstractDoCommandInterceptor.java:29) / HighlightInterceptor.invoke(HighlightInterceptor.java:28) / AbstractDoCommandInterceptor.invoke(AbstractDoCommandInterceptor.java:29) / CommandLogInterceptor.invoke(CommandLogInterceptor.java:72) / AbstractDoCommandInterceptor.invoke(AbstractDoCommandInterceptor.java:29) / CommandList.execute(CommandList.java:150) / TestCase.execute(TestCase.java:299) / ExecuteTestCaseInterceptor.invoke(ExecuteTestCaseInterceptor.java:49) / AbstractExecuteTestCaseInterceptor.invoke(AbstractExecuteTestCaseInterceptor.java:29) / TestSuite.execute(TestSuite.java:158) / ExecuteTestSuiteInterceptor.invoke(ExecuteTestSuiteInterceptor.java:49) / AbstractExecuteTestSuiteInterceptor.invoke(AbstractExecuteTestSuiteInterceptor.java:29) / Runner.execute(Runner.java:568) / Runner.run(Runner.java:612) / Main.run(Main.java:83) / Main.main(Main.java:213))] URL: [https://www.inno.be/nl-be/] / Title: [GALERIA Inno: Webshop voor kleding, beauty & speelgoed]
[2016-11-15 05:30:45.552 +01:00] [ERROR] - Cookie: bid=[0100007F578F2A58D57A3CB802A50303] (domain=www.inno.be, path=/, expire=2017-11-15 05:30:15 +01:00)
[2016-11-15 05:30:45.552 +01:00] [ERROR] - Cookie: vid=[fwAAAVgqj1e4PHrVAwOlAg==] (domain=www.inno.be, path=/, expire=2016-12-15 05:30:15 +01:00)
[2016-11-15 05:30:45.554 +01:00] [INFO] End(30,510sec): TestCase[complex] (/var/tmp/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF.html)
[2016-11-15 05:30:45.555 +01:00] [INFO] End(0,000sec): TestSuite[complex] (/var/tmp/journeymonitor-testcase-6258D6F5-FE7C-44D5-8B41-324FDAE97CAF.html)
[2016-11-15 05:30:45.555 +01:00] [INFO] Exit code: 4
[2016-11-15 05:30:45.630 +01:00] [INFO] Quit: FirefoxDriver
EOT;
        $la = new LogAnalyzer();
        $this->assertTrue($la->pageloadTimeoutOccured(explode("\n", $output)));
    }

    public function testGetUrlsOfRequestedPages()
    {
        $logContent = file_get_contents(__DIR__ . '/../../../fixtures/selenese-runner.log');

        $la = new LogAnalyzer();

        $actualListOfUrls = $la->getUrlsOfRequestedPages($logContent);
        $expectedListOfUrls = [
            0 => 'https://www.galeria-kaufhof.de/',
            1 => 'https://www.galeria-kaufhof.de/search?q=hose',
            2 => 'https://www.galeria-kaufhof.de/#foo'
        ];

        $this->assertEquals($expectedListOfUrls, $actualListOfUrls);
    }
}
