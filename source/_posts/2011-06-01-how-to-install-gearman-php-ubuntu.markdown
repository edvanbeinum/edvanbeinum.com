---
layout: post
title: "How To Install Gearman for PHP on Ubuntu"
date: 2011-06-01 18:21

---
This will grab serveral Gearman packages includings gearman-job-server and gearman-worker among others

    $ sudo apt-get install -y php5-dev gearman libgearman-dev

PHP5-dev package is needed to get phpize which we use shortly to create the PHP gearman library. 

Get the latest version of the PHP wrapper for libgearman
``` bash
$ wget http://pecl.php.net/get/gearman
$ tar -zxf gearman
$ cd gearman-X.X
$ phpize
$ ./configure
$ make && make install
```
    

Then add 

    extension=gearman.so

to /etc/php/cli/php.ini and /etc/php5/apache2/php.ini (look around line 940 on a typical php.ini file for the Dynamic Modules section)

That should be it, use the PHP REPL to check:

    $ php -a
    print gearman_version();

I got the following warning:

    PHP Warning:  PHP Startup: Unable to load dynamic library '/usr/lib/php5/20090626/gearman.so' - /usr/lib/php5/20090626/gearman.so: cannot open shared object file: No such file or directory in Unknown on line 0

so I had to manually copy the Gearman extension to the right directory (assuming you are still in your gearman-X.X directory):

    $ sudo cp modules/gearman.so /usr/lib/php5/20090626/

Boom!

Official docs:    
[PECL](http://pecl.php.net/package/gearman)   
[Gearman.org](http://gearman.org/index.php?id=getting_started)