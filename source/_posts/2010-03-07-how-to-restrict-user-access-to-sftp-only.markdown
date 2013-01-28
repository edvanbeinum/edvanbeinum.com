---
layout: post
title: "How to restrict user access to SFTP only"
date: 2010-03-07 12:28

---
FTP is horrible so here is how to setup up a user on Linux box and grant them SFTP access only instead of giving them full SSH access.<br />As the root user, create a new user with:

    useradd -d /var/www -s /usr/lib/sftp-server bob

This adds a user called bob, -d specifies their home directory and -s specifies their login shell. In this case this a user who we want to be able to edit files on our webserver.Give them a password

    passwd bob

Then you have to add the SFTP shell to the list if valid shells in /etc/shells. You can do that via the text editor of your choice; add the line to the bottom of /etc/shells

    /usr/lib/stfp-server

Or a nicer way to do that is

    echo '/usr/lib/sftp-server' >> /etc/shells

The '>>' character is a redirection operator. So that command means "redirect the output of the command before the >> and append that to the file named after the >>".Make sure you use two angle brackets; one '>' will overwrite the existing file rather than appending it. (thanks <a href="http://twitter.com/QuantumLoops/">Chris</a>) You can read more on redirection operators at <a href="http://wiki.linuxquestions.org/wiki/Redirection_operator">http://wiki.linuxquestions.org/wiki/Redirection_operator</a>