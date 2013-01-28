---
layout: post
title: "Roll your own hotlink protection"
date: 2009-03-05 05:26

---
I group WHM/cPanel with things such as WYSIWYG HTML editors, reusing passwords and mobile phones; useful but you should know when not to use them.

Using cPanel for any type of .htacces modification is one of those times. cPanel happily rewrites .htaccess files as it wishes and provides no warning that your carefully written rules will be lost. Turning on 'hotlink protection' in cPanel does odd things if you have any existing URL rewrite rules; as far as I could see it just removed the line:

    RewriteEngine On

and that was it, thus breaking the site <em>and</em> not offering hotlink protection. Marvellous.

What cPanel does (or tries to do) is to add these lines to your .htaccess when you click 'enable hotlink protection':


    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http://yourdomain.com/.*$      [NC]
    RewriteCond %{HTTP_REFERER} !^http://yourdomain.com$      [NC]
    RewriteCond %{HTTP_REFERER} !^http://www.yourdomain.com/.*$      [NC]
    RewriteCond %{HTTP_REFERER} !^http://www.yourdomain.com$      [NC]
    RewriteRule .*\\.(jpg|jpeg|gif|png|bmp)$ http://yourdomain.com [R,NC]

In the above example it allows access from yourdomain.com and www.yourdomain.com and any folders within and then any server with a domain name not matching those who requests files with the extension .jpg, .jpeg, .gif, .png and .bmp is redirected to http://yourdomain.com. This results in a broken image on the hotlinking site. If you wish your logo to appear instead of the hotlinked image then alter the last line to something like:

    RewriteRule .*\\.(jpg|jpeg|gif|png|bmp)$ http://yourdomain.com/logo.gif [R,NC]
