---
layout: post
title: "Retrieve all Userdata from CodeIgniter's Session library"
date: 2010-03-31 16:31

---
This is <a href="http://codeigniter.com/user_guide/libraries/sessions.html">undocumented</a> as far as I can tell, but to retrieve an array of all Userdata stored in a Session you can use:

    $this->session->all_userdata()