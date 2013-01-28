---
layout: post
title: "How to use PHP ActiveRecord with Zend Framework"
date: 2011-01-22 19:05

---
[php-activerecord](http://www.php-activerecord.org/) is a rather nice ORM written in PHP 5.3 which I am quite fond of. Compared to figuring out how to use Zend\_Db\_Table, setup fields and entity relationships, this is a breeze. php-activerecord makes use of convention over configuration, and since database schemas for most web apps are conventional, this works out just fine. Anyway, *how* it works and why I like it is a blog post for another time.

A quick search on the subject reveals two pretty [good](http://spokefire.co.uk/blog/2010/7/using-php-activerecord-with-zend-framework) [posts](http://www.derivante.com/2009/05/21/php-rapid-application-development-with-zf18-and-ar/) on the suject of setting up php-activerecord and Zend Framework, but the main issue with both of them is that they put application configuration data (in this case the database connection details) in the bootstrap.php file, which doesn't quite sit right with me. So my approach has a small tweak so that configuration data stays in the application.ini file and the bootstrap file just does the bootstrapping. All nice and tidy, which makes me happy.

1. Once you have got php-activerecord put the folder in

        library/ActiveRecord


2. Set up your database connection details in application/configs/application.ini

        resources.db.params.host = *hostname*
        resources.db.params.username = *username*
        resources.db.params.password = *password*
        resources.db.params.dbname = *database name*


3. Now in your application/bootstrap.php file you need to get access to application.ini. This next part comes from the Zend Framework master, Rob Allen and his post [Accessing your configuration data that's stored in application.ini](http://akrabat.com/zend-framework/accessing-your-configuration-data-in-application-ini/). To paraphrase, once Zend Framework has used the application.ini for what it needs to do, it discards its reference to it so you cannot access it later from your own code without going and getting it yourself. So what you need to do is to grab it and put it in the Zend\_Registry. Yes it is true: Registry = Global = Evil, but for storing configuration data I think we can permit its use. So we create a method in bootstrap.php to do exactly that (all protected methods in bootstrap.php whose names start with '\_init' get called automatically by Zend Framework, and if those method return a value we can access that value from elsewhere in our bootstrap class, as you will see a little later on the first line of the _initPhpActiveRecord() method):

        protected function _initConfig()
        {
                $config = new Zend_Config($this->getOptions());
                Zend_Registry::set('config', $config);
                return $config;
        }


4. Finally we can load up php-activerecord with another _init function in bootstrap.php:

        protected function _initPhpActiveRecord()
        {
                $appConfig = $this->config;
                $dbParams = $appConfig->resources->db->params;
                $dsn = "mysql://{$dbParams->username}:{$dbParams->password}@" .
                        "{$dbParams->host}/{$dbParams->dbname}";
                Zend_Loader::loadFile('ActiveRecord.php', '../library/ActiveRecord',
                        TRUE);
                ActiveRecord\Config::initialize(
                        function($cfg) use ($dsn) {
                                $cfg->set_model_directory(APPLICATION_PATH . '/models');
                                $cfg->set_connections(array('development' => $dsn));
                        }
                );
        }

This grabs the config data (not actually from th Registry - since our \_initConfig returned a value we can access it from elsewhere in our Bootstrap class), and creates a Data Source Name string using the database parameters we have stored in our application.ini file. We then use Zend\_Loader to load our php-activerecord library and then using a fancy-pants PHP 5.3 [closure](http://codeutopia.net/blog/2009/02/20/closures-coming-in-php-53-and-thats-a-good-thing/) we initalise php-activerecord with the correct Model Directory and correct database connection details.

Then you can create your php.activerecord Models in your /application/models directory and call them from your Controllers like so:

    $products = Product:all();

Now we need a quick note on 'production' and 'development' database connections. "Spokefire's example had different connections for each environment! I want that!" I can almost hear you cry. Well we actually already have that functionality happening. Here's how:

In the set\_connections() method of the Config class we passed an associative array with the key 'development' and the value of the data source name. We *could* use php-activerecord's internal system for setting which environment we want but then we would have to set the environment in both Zend Framework AND php-activerecord, which I *will* forget to do. So in this case php-activerecord will always use its own internal 'development' environment setting but, and this is the crux of the matter, Zend Framework will pass the correct database connection details to it. Because we are using application.ini we can make use of Zend Framework's environment settings that will automatically pick out the correct database parameters from application.ini. So once our application.ini is all setup, we don't have to think about it again. Just set our  APPLICATION\_ENV to 'production' and it will used!