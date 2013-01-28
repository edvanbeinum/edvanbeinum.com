---
layout: post
title: "Suspicious server activity..?"
date: 2008-11-11 16:29

---
<p>	Recently a client had noticed some odd spikes in their bandwidth usage on one of their servers and they asked me to have a look at what was causing it. So here a some useful places to start. This is on a CentOS 5 server with WHM/Cpanel. One of the first things to look at is the Bash history 

        cat -n .bash_history

 Also look at access logs 

        vim /var/log/messages

 and 


        vim /var/log/secure

 If you suspect something untoward is happen this instant running this 

        find / -mmin -10

 will give you a list of the any files modified in the last 10 minutes. (also handy when you download something and forget where you put it) or for the last day 

        find . -mtime 0

 If you're running a server with WHM/cPanel then some useful logs to have a look at Exim: 

        /var/log/exim_mainlog
        /var/log/maillog
        /var/log/exim_paniclog

 Apache (404 not found errors, etc): 

         /usr/local/apache/logs/error_log

 Access Log: 

        /usr/local/apache/logs/access_log

 Site Logs: 

        /usr/local/apache/domlogs/sitename.com

 Logins: 

        var/log/secure /var/log/logins_log

 Messages: 

        var/log/messages

 cPanel: 

        /usr/local/cpanel/logs/access_log

 For ongoing monitoring if you aren't getting LogWatch emails in your inbox each morning, then you are missing out on quite a treat (*treat is related to the recipient's level of geekery). You may well have it already on your server 

        which logwatch

 but not know it if you don't get your root email as it sends mail to root by default. Otherwise Logwatch is available via most Linux package managers 

        apt-get logwatch

 OR 

        yum install logwatch

 and the like. Take a look at the conf file - usually at 

        /etc/log.d/conf/logwatch.conf
        
 and play around with the settings - the file is well commented and has some nice examples. I have the detail level ramped up to 5 which spits out more info than you'll ever really need but it keeps you safe in the knowledge that you server is behaving as it should. If this kind of thing floats your boat this tale of <a href="http://blog.gnist.org/article.php?story=HollidayCracking">Holiday hack investigating</a> makes a good read. And the thrilling dénouement to my client's bandwidth spikes? I'd misconfigured the backup schedule and it was running a full offsite backup twice as frequently as before.