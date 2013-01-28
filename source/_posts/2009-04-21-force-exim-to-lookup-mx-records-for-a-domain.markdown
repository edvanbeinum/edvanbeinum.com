---
layout: post
title: "Force Exim to lookup MX records for a domain"
date: 2009-04-21 17:38

---
Say you have a domain hosted on your server but have its MX records set up to to use an external SMTP server, it may cause you to be unable to send email from another domain hosted on your server to the first domain - if you can unpick that convoluted sentence!Exim will receive the email addressed to the first domain and it will think it is as local domain so it will route the email straight there without querying the MX records you have set. What happens to the message next depends on how you have Exim set up to handle email where the user doesn't exist. WHM has the default setting that it is sent to the root user account of that domain which menas you won't get a bounce message and the intended recipient won't receive your email either.This can be fixed by editing

    /etc/localdomains

and removing the domain from the list and then editing

    /etc/remotedomains

and adding the domain there.Now email should be routed properly and Exim will look up the MX records for that domain and send email there.