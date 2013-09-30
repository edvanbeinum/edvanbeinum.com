---
layout: post
title: "Using Selenium 2 &amp; PHPUnit to automate browser testing "
date: 2011-12-20 02:00

---
I'd like to pretend that this article is written out of altruism, but really this is just for myself since this is the second time I've had to learn this stuff.

This is a glossary of terms and tech as I understand them. Please do [correct me](http://twitter.com/edvanbeinum) if I'm wrong.

* __Selenium 2 Server__ is the thing that creates instances of browsers for you. 
* __Webdriver__ is the protocol (using JSON) used to communicate with those browser instances 
* __PHP WebDriver__ is a library that wraps the WebDriver protocol in PHP so we can use PHP to interact with the Selenium2 Server.
* __Selenium 1__ is dead

### Tool up

Get the [PHP WebDriver library](https://github.com/facebook/php-webdriver) by a nice chap at Facebook 

and get the [Selenium 2 server standalone JAR](http://seleniumhq.org/download/) (version 2.15.0 at the time of writing).

And you'll need Java installed - openJDK is probably best as it is now the official JDK and the [Sun JDK is being forcibly removed from Ubuntu](http://www.omgubuntu.co.uk/2011/12/java-to-be-removed-from-ubuntu-uninstalled-from-user-machines)

Start your engines:

``` bash
	java -jar /path/to/selenium-server-standalone-2.15.0.jar
```
Selenium2 Server is now running at http://localhost:4444/wd/hub by default and PHP Webdriver will connect to that automatically.

### Write browser tests in PHPUnit

We can use PHPUnit as the test runner and pull in the PHP Webdriver library into our testsuite and create automated browser tests.
Move the PHP Webdriver library somewhere where you can access it from your tests, I personally like to put a lib/ folder inside my tests/ folder for each project.

``` php
	require_once __DIR__ . '/lib/php-webdriver/__init__.php';

	class HomePageTest extends PHPUnit_Framework_TestCase
	{
		/** 
		* @var WebDriverSession
		*/
	    protected $_session;

	    public function setUp()
	    {
	        parent::setUp();
	        $web_driver = new WebDriver();
	        $this->_session = $web_driver->session();
	    }

	    public function tearDown()
	    {
	        $this->_session->close();
	        unset($this->_session);
	        parent::tearDown();
	    }

	    public function test_speaker_navigation_tab_goes_to_speaker_page()
	    {
	        $this->_session->open('http://conference.development.local/');

	        $this->_session->element(
		        'xpath', 
		        '/html/body/div/header/div[2]/ul/li[3]/a'
		    )->click();

	        $this->assertSame(
		        'http://conference.development.local/speakers', 
		        $this->_session->url()
		    );
	    }
	}
```

Here you can see that we have a regular PHPUnit test case but we can use the WebDriver library to execute browser commands. 
The first line pulls in the PHP WebDriver library (in a real project you will probably want to put that require statement into a bootstrap file and call it via the bootstrap parameter in PHPUnit's config XML file so it is automatically included before each test is run).
Nothing too fancy is going on, we create a new instance of the WebDriver class and and then store an instance of WebDriverSession as a class member which is the object does all the browser automation for us. Calling WebDriver::session() with no parameters will use Firefox by default. See below for how to use Chrome.

The actual test is contrived; I'm working on a site for a conference and I want to check that when the 'speakers' tab is clicked in the navigation, it takes us to the expected Speakers page URL. WebDriverSession::element() takes two parameters, the 'locator strategy' and the value. 'Locator strategy' is just how you want to select the element: by CSS id , class, or selector or Xpath or even link text. There are a few others too, you can see the full list of [locator strategies](http://code.google.com/p/selenium/wiki/JsonWireProtocol#/session/:sessionId/element). 

We then assert that the session URL is the same and the expected URL and, hopefully, our tests pass.

### Using Chrome

To run the tests in Chrome, you will need to download the [ChromeDriver](http://code.google.com/p/chromium/downloads/list) for your OS. Extract it and put it somewhere on your PATH (on OSX/Linux either put in or symlink to /usr/local/bin). Now when you call WebDriver::session() pass it 'chrome' as a string:

	$this->_session = $web_driver->session('chrome');

and that will launch and instance of Chome and run the tests.

### Futher Reading

* [JsonWireProtocol](http://code.google.com/p/selenium/wiki/JsonWireProtocol)
* [PHP WebDriver wiki](https://github.com/facebook/php-webdriver/wiki) and [readme](https://github.com/facebook/php-webdriver/blob/master/README.md)
* [Selenium2 and PHPUnit on StackOverflow](http://stackoverflow.com/a/7646333)