---
layout: post
title: "Taming WordPress error pages"
date: 2009-03-03 05:11

---
WordPress gets a bit enthusiastic about putting it's 404 error pages about the place - even redirecting 401 and 403s. So to allow you to use .htaccess password protected directories whilst also running WP insert these lines into your root .htaccess

    ErrorDocument 401 /PATH_TO_PASSWORD_PROTECTED_DIR
    ErrorDocument 403 /PATH_TO_PASSWORD_PROTECTED_DIR
