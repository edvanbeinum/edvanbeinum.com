---
layout: post
title: "Quick and dirty installation of FFMPEG on Linux (Centos)"
date: 2010-01-01 12:53

---
A client needed <a href="http://ffmpeg.org/">FFMPEG</a> installed on a Centos Server for use with SlideShow Pro Director. Here is a quick method using Yum rather than compiling the source code, assuming you have root privileges: Add a new repository for Yum 

    vim /etc/yum.repos.d/dag.repo

 and add the following repo details to it 
 
    [dag] 
    name=Dag RPM Repository for Red Hat Enterprise Linux 
    baseurl=http://apt.sw.be/redhat/el$releasever/en/$basearch/dag gpgcheck=1 enabled=1

 Then install FFMpeg
    
    yum install ffmpeg ffmpeg-devel

I then got an error about not having a public key installed. So we need to import the public key for the Dag RPM repo which is available here: http://dag.wieers.com/rpm/packages/RPM-GPG-KEY.dag.txt 

    rpm --import http://dag.wieers.com/rpm/packages/RPM-GPG-KEY.dag.txt

 Thanks to http://www.mysql-apache-php.com/ffmpeg-install.htm for the kick-start on this.</p>