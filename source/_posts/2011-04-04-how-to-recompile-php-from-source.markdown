---
layout: post
title: "How to recompile PHP from source"
date: 2011-04-04 18:17

---
[Habari 0.7](http://www.habariproject.com) was released on Friday, which is ace. They have improved the internalisation of the project and as a result it now requires PHP's [mbstring](http://www.php.net/manual/en/book.mbstring.php) to be installed on the server. I didn't have that extension installed (I try and keep to the principle of [minimalism](http://cloudservers.rackspacecloud.com/index.php/Minimalism) where I can) and Debain doesn't have an Apt package for mbstring, so here is how to recompile PHP with mbstring... but this should work for any other extension you should care to add.

As far as I'm aware you can't add new modules to an existing PHP installation (without using a package manager like APT or Yum), you have to recompile the whole thing including the original configure commands and add on the new ones.

I, of course, forget what [configure options](http://www.php.net/manual/en/configure.about.php) I used when I built PHP. Luckily PHP knows (An [elephant](http://www.flickr.com/photos/dotjay/2345448542/) never forgets. Ahem).
So run this command which will tell you which commands were used when PHP was compiled and make a note of them:

        $ php -i | grep Configure

Now because I'm lazy, I usually find that I left the PHP source code directory that I downloaded on the server so I'll find it in /usr/src or /usr/local/src. If you're super-organised and have removed the source directory then go grab the latest [PHP release](http://php.net/downloads.php) and un-tar it. Either way, make sure you are in the un-tarred PHP directory and take the command that you previously made a note of and then append your changes, in my case I needed to add '--enable-mbstring' 

        $ ./configure  --with-curl --enable-fpm --enable-mbstring

and then

        $ make && make install

Then bask in your awesomeness.