---
layout: post
title: "Remove conflicted copies from Dropbox"
date: 2010-08-14 16:43

---
I love [Dropbox](http://www.dropbox.com/referrals/NTEyNDM0NTk5) (yes that is a cheeky referral link), it has saved my ass on numerous occasions. I use it to have a shared Firefox Profile across 3 machines which works great. However it does sometimes cause conflicts which mean that Dropbox will append 'conflicted copy' to the filename. These multiple copies of files will be using up more space than really necessary, so I wrote a rough and ready shell script that deletes any conflicted copies from your Dropbox folder. It also outputs the names of the files deleted.
This is Linux only:

    find $HOME/Dropbox -name <em>conflicted</em> -exec rm {} \; -exec echo {} \;

This assumes that Dropbox uses a folder called 'Dropbox' in your Home directory - so change '$HOME/Dropbox' as necessary. I've saved that an shell script and set a cron job to run it once a day.

Possible improvements to make when I have time (and have improved my shell scripting!):

- Report the amount of disk space reclaimed by removing files
- Report the number of files removed
- Perhaps a confirmation before any files are removed
- Windows version (As a batch file?)
