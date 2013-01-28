---
layout: post
title: "How to install and configure Supervisord "
date: 2011-07-30 18:47

---
Well, I can't claim any more credit to the content here than amalgamating two blog pages:

[Sean Coates' entry from 2009 PHP Advent](http://phpadvent.org/2009/daemonize-your-php-by-sean-coates)   
[Supervisord docs](http://supervisord.org/installing.html#installing-to-a-system-with-internet-access)

but hey, now I only have to look in one place in a few months when I've completely forgotten how to do this.  
Install Supervisord on Ubuntu by doing the following

    sudo apt-get install python-setuptools
    sudo easy_install supervisor
    sudo su root
    echo_supervisord_conf > /etc/supervisord.conf

That last command will create a default configuration file which we can now edit. To tell Supervisord which command you want to run as a daemon open up /etc/supervisord.conf and add the program declaration

    [program:ENTER DESCRIPTIVE NAME]
    command=ENTER COMMAND TO BE DAEMONIZED HERE
    numprocs=1
    directory=ENTER DIRECTORY FROM WHICH THE COMMAND SHOULD BE RUN
    stdout_logfile=ENTER FULL PATH TO LOG FILE
    autostart=true
    autorestart=true
    user=ENTER THE LINUX USER WHO WILL EXECUTE THE COMMAND

Then make the log directory

    mkdir -p ENTER FULL PATH TO LOG FILE

Now we need to make an init.d script so we can start and stop Supervisord easily. Into /etc/init.d/supervisord paste the following

    #! /bin/bash -e

    SUPERVISORD=/usr/local/bin/supervisord
    PIDFILE=/tmp/supervisord.pid
    OPTS="-c /etc/supervisord.conf"

    test -x $SUPERVISORD || exit 0

    . /lib/lsb/init-functions

    export PATH="${PATH:+$PATH:}/usr/local/bin:/usr/sbin:/sbin"

    case "$1" in
      start)
        log_begin_msg "Starting Supervisor daemon manager..."
        start-stop-daemon --start --quiet --pidfile $PIDFILE --exec $SUPERVISORD -- $OPTS || log_end_msg 1
        log_end_msg 0
        ;;
      stop)
        log_begin_msg "Stopping Supervisor daemon manager..."
        start-stop-daemon --stop --quiet --oknodo --pidfile $PIDFILE || log_end_msg 1
        log_end_msg 0
        ;;

      restart|reload|force-reload)
        log_begin_msg "Restarting Supervisor daemon manager..."
        start-stop-daemon --stop --quiet --oknodo --retry 30 --pidfile $PIDFILE
        start-stop-daemon --start --quiet --pidfile /var/run/sshd.pid --exec $SUPERVISORD -- $OPTS || log_end_msg 1
        log_end_msg 0
        ;;

      *)
        log_success_msg "Usage: /etc/init.d/supervisor
    {start|stop|reload|force-reload|restart}"
        exit 1
    esac

    exit 0

Save and then execute the following two commands which will make the script executable and also make sure the Supervisord process is started upon rebooting the server.

    sudo chmod +x /etc/init.d/supervisord
    sudo update-rc.d supervisord defaults

Finally start Supervisord

    sudo service supervisord start
