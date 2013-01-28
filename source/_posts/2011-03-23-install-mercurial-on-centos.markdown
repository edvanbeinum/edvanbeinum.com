---
layout: post
title: "How to Install Mercurial on CentOS"
date: 2011-03-23 20:18

---
This has been tested on CentOS 5.5 (and assumes you have root privileges).

First, install a couple of dependencies: a C compiler and the necessary Python C headers
    $ yum install gcc
    $ yum install python-devel

Then Docutils for Python is needed for generating the documentation. There is no Yum package for that so do this:

    $ wget http://docutils.sourceforge.net/docutils-snapshot.tgz
    $ tar -xvf docutils-snapshot.tgz
    $ cd docutils
    $ python setup.py install

Then we are ready to install Mercurial (1.8 was the latest version at the time of writing)

    $ wget http://mercurial.selenic.com/release/mercurial-1.8.tar.gz
    $ tar xvzf mercurial-1.8.tar.gz
    $ cd mercurial-1.8
    $ make install

And that's it. You can sanity check the installation by running:

    $ hg debuginstall

Read the official Unix [Mercurial Installation Guide](http://mercurial.selenic.com/wiki/UnixInstall)