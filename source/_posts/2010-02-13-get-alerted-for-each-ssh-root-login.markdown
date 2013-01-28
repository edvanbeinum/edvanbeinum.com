---
layout: post
title: "Get email alerts for each SSH root login to your server"
date: 2010-02-13 09:54

---
There are 3 files that can be run once a user logs in (and the Bash Shell starts) and we will add a line in one of these which will email a notification to a given email address whenever the root user logs in. The 3 possible files are in the root user's home directory:

* .bash_profile
* .bash_login
* .profile

Bash looks for those scripts in that order and once it has found a file that matches that filename, it and only it, is run. For example, if there is a .bash_profile file, any commands in .bash_login will not be called.

So once you have logged in as root

    $ cd
    $ ls -al

This will give you a list of all files in root's home directory and see which of the above three files exist and open the one that gets called first. Insert this line

    echo "ALERT - Root Shell Access on:" `date` `who` | mail -s "Alert: Root Access on SERVER" YOU@DOMAIN.COM

Change SERVER to your server name and YOU@DOMAIN.COM to your email address.
Then logout and back in again and check your inbox for a notification