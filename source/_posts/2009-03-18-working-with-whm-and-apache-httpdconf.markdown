---
layout: post
title: "Working with WHM and Apache httpd.conf"
date: 2009-03-18 07:54

---
WHM automatically generates your httpd.conf (usually at /usr/local/apache/conf/httpd.conf) and so any manual changes you make will be overwritten. To make changes open httpd.conf and under the correct VirtualHost check where the commented Include file path points, something like

    /usr/local/apache/conf/userdata/std/1/USERNAME/DOMAIN/*.conf"

Create a file and that directory tree and put your VirtualHost directives in there and save. Then rebuild httpd.conf by 

    /usr/local/cpanel/bin/build_apache_conf
and then restart Apache 

    /usr/sbin/httpd restart

 and away you go. <em>Note</em>: This is for Apache 1.3. Apache 2 uses a different site-available/sites-enabled system