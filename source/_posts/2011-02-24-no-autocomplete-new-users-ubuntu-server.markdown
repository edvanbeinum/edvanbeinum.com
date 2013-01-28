---
layout: post
title: "No autocomplete for new users on Ubuntu Server"
date: 2011-02-24 17:18

---
This is a gotcha that  got me today; new users on Ubuntu Server are given the Bourne shell rather than the Bash shell. This means that after adding a user with

    $ useradd username

that user will have no command autocompletion and no current directory shown before the command prompt.
The user's shell can be changed using:

    $ chsh -s /bin/bash username

To change the default shell, look in 

    $ /etc/defaults/useradd

and there is a SHELL directive in there that can be changed to

    $ SHELL=/bin/bash